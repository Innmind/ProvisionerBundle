<?php

namespace Innmind\ProvisionerBundle;

use Innmind\ProvisionerBundle\Server\ServerInterface;
use Innmind\ProvisionerBundle\Event\ProvisionEvents;
use Innmind\ProvisionerBundle\Event\ProvisionRequirementEvent;
use Innmind\ProvisionerBundle\Event\ProvisionEvent;
use Innmind\ProvisionerBundle\Event\ProvisionAlertEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\LockHandler;
use Psr\Log\LoggerInterface;

/**
 * Class acting how many processes can be launched on the server
 */
class DecisionManager
{
    protected $server;
    protected $cpuThreshold;
    protected $processStatus;
    protected $dispatcher;
    protected $logger;

    /**
     * Set the server helper
     *
     * @param ServerInterface $server
     */
    public function setServer(ServerInterface $server)
    {
        $this->server = $server;
    }

    /**
     * Set the CPU top threshold
     *
     * @param float $threshold
     */
    public function setCpuThreshold($threshold)
    {
        $this->cpuThreshold = (float) $threshold;
    }

    /**
     * Set the process status handler
     *
     * @param ProcessStatusHandler $handler
     */
    public function setProcessStatusHandler(ProcessStatusHandler $handler)
    {
        $this->processStatus = $handler;
    }

    /**
     * Set the event dispatcher
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Set the logger
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Return the number of processes that can be launched on the server
     *
     * @param string $command Command name to analyze
     *
     * @return int
     */
    public function getAllowedProcesses($command)
    {
        $cpuUsage = $this->server->getCpuUsage();

        $averageProcessUsage = $this->processStatus->getProcessUsage($command);
        $averageProcessNumber = $this->processStatus->getProcessCount($command);

        if ($cpuUsage >= $this->cpuThreshold) {
            return 0;
        }

        if ((int) $averageProcessNumber === 0) {
            return 1;
        }

        $availableCpu = $this->cpuThreshold - $cpuUsage;
        $processUsage = $averageProcessUsage / $averageProcessNumber;

        return (int) floor($availableCpu / $processUsage);
    }

    /**
     * Instruct the bundle to run all the process it can
     * and dispatch alerts if necessary
     *
     * @param string $name Symfony command name being run
     * @param array $args Command input arguments
     */
    public function provision($name, array $args)
    {
        $lockHandler = new LockHandler(sprintf(
            'provision.%s.lock',
            preg_replace('/[:\/]/', '-', $name)
        ));

        if (!$lockHandler->lock()) {
            return;
        }

        if ($this->logger) {
            $this->logger->info(
                'Starting provisioning',
                ['command' => $name, 'args' => $args]
            );
        }

        $event = $this->dispatcher->dispatch(
            ProvisionEvents::COMPUTE_REQUIREMENTS,
            new ProvisionRequirementEvent($name, $args)
        );

        $required = $event->getRequiredProcesses();

        $command = sprintf(
            'console %s %s',
            $name,
            CommandHelper::getArgumentsAsString($args)
        );

        $allowed = $this->getAllowedProcesses($command);

        if ($required > $allowed) {
            $toRun = $allowed;
            $leftOver = $required - $allowed;
        } else {
            $toRun = $required;
            $leftOver = 0;
        }

        $this->dispatcher->dispatch(
            ProvisionEvents::PROVISION,
            new ProvisionEvent($name, $args, $toRun)
        );
        $this->dispatcher->dispatch(
            ProvisionEvents::ALERT,
            new ProvisionAlertEvent($name, $args, $leftOver)
        );

        if ($this->logger) {
            $this->logger->info(
                'Finished provisioning',
                [
                    'command' => $name,
                    'args' => $args,
                    'processes_spawned' => $toRun,
                    'left_overs' => $leftOver,
                ]
            );
        }

        $lockHandler->release();
    }
}
