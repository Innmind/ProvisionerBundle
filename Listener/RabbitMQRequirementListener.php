<?php

namespace Innmind\ProvisionerBundle\Listener;

use Innmind\ProvisionerBundle\ProcessStatusHandler;
use Innmind\ProvisionerBundle\RabbitMQ\HistoryInterface;
use Innmind\ProvisionerBundle\RabbitMQ\Admin;
use Innmind\ProvisionerBundle\Event\ProvisionRequirementEvent;
use Innmind\ProvisionerBundle\Math;
use Innmind\ProvisionerBundle\CommandHelper;
use Symfony\Component\Process\Process;
use Psr\Log\LoggerInterface;

class RabbitMQRequirementListener
{
    protected $processStatus;
    protected $queueHistory;
    protected $admin;
    protected $logger;

    /**
     * Set process status handler
     *
     * @param ProcessStatusHander $handler
     */
    public function setProcessStatusHandler(ProcessStatusHandler $handler)
    {
        $this->processStatus = $handler;
    }

    /**
     * Set the queue history object to help retrieve last
     * queue depth number
     *
     * @param HistoryInterface $history
     */
    public function setQueueHistory(HistoryInterface $history)
    {
        $this->queueHistory = $history;
    }

    /**
     * Set the rabbitmq admin interface
     *
     * @param Admin $admin
     */
    public function setRabbitMQAdmin(Admin $admin)
    {
        $this->admin = $admin;
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
     * Compute how many consumers needs to be run
     *
     * @param ProvisionRequirementEvent $event
     */
    public function handle(ProvisionRequirementEvent $event)
    {
        if ($event->getCommandName() !== 'rabbitmq:consumer') {
            return;
        }

        $args = $event->getCommandArguments();

        $command = sprintf(
            'console %s %s',
            $event->getCommandName(),
            CommandHelper::getArgumentsAsString($args)
        );

        $consumers = $this->processStatus->getProcessCount($command);
        $messages = (int) $event->getCommandArguments()['messages'];

        $queue = $event->getCommandArguments()[0];

        $depth = $this->admin->listQueueMessages($queue);

        $depthHistory = $this->queueHistory->get(sprintf(
            '%s.queue_depth',
            $queue
        ));

        if ($this->logger) {
            $this->logger->info(
                'Estimating required consumers processes',
                [
                    'queue_depth' => $depth,
                    'previous_depth' => end($depthHistory),
                ]
            );
        }

        $depthHistory[] = $depth;

        if (count($depthHistory) > 1) {
            $estimated = $this->getEstimatedDepth($depthHistory);
            $depth = $estimated['current'];
            $previousDepth = $estimated['previous'];
        } else {
            $previousDepth = 0;
        }

        $event->setRequiredProcesses(
            $this->computeRequirement(
                $previousDepth,
                $depth,
                $messages,
                $consumers
            )
        );
        $event->stopPropagation();

        $this->queueHistory->put(sprintf(
            '%s.queue_depth',
            $queue
        ), $depthHistory);

        if ($this->logger) {
            $this->logger->info(sprintf(
                'Required consumers processes estimated to %s',
                $event->getRequiredProcesses()
            ));
        }
    }

    /**
     * Determine how many consumers to launch based on previous
     * messages count and the new one, and the available consumers
     *
     * @param float $previousDepth
     * @param float $currentDepth
     * @param int $messages Messages consumed by one consumer
     * @param int $consumers Running consumers
     *
     * @return int
     */
    public function computeRequirement($previousDepth, $currentDepth, $messages, $consumers)
    {
        if ((int) $previousDepth === 0) {
            if (
                (float) $currentDepth < (int) $messages &&
                (int) $consumers >= 1
            ) {
                $consumersToLaunch = 0;
            } else {
                $consumersToLaunch = 2;
            }
        } else {
            $diff = (float) $currentDepth - (float) $previousDepth;

            if ($diff <= 0) {
                $consumersToLaunch = 1;

                if (
                    (float) $currentDepth < (int) $messages &&
                    (int) $consumers >= 1
                ) {
                    $consumersToLaunch = 0;
                }
            } else {
                $consumersToLaunch = (int) floor($diff / $messages);
            }
        }

        return $consumersToLaunch;
    }

    /**
     * Return estimated previous and current depth once
     * a linear regression is applied to the real history
     *
     * @param array $history
     *
     * @return array As ['previous' => float, 'current' => float]
     */
    public function getEstimatedDepth(array $history)
    {
        $regression = Math::linearRegression($history);

        $previous = (count($history) - 2) * $regression['slope'] + $regression['intercept'];
        $current = (count($history) - 1) * $regression['slope'] + $regression['intercept'];

        return [
            'previous' => $previous,
            'current' => $current,
        ];
    }
}
