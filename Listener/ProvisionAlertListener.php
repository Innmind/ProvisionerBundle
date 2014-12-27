<?php

namespace Innmind\ProvisionerBundle\Listener;

use Innmind\ProvisionerBundle\Event\ProvisionAlertEvent;
use Innmind\ProvisionerBundle\Alert\AlerterInterface;
use Innmind\ProvisionerBundle\Server\ServerInterface;

/**
 * Alerts when new servers need to be run or the current server
 * is no longer needed
 */
class ProvisionAlertListener
{
    protected $alerters = [];
    protected $cpuThreshold;
    protected $loadAverageThreshold;
    protected $server;

    /**
     * Add a new alerter
     *
     * @param string $type
     * @param AlerterInterface $alerter
     */
    public function addAlerter(AlerterInterface $alerter)
    {
        $this->alerters[] = $alerter;
    }

    /**
     * Set the CPU thresholds
     *
     * @param int $min
     * @param int $max
     */
    public function setCpuThresholds($min, $max)
    {
        $this->cpuThreshold = [(int) $min, (int) $max];
    }

    /**
     * Set the load average thresholds
     *
     * @param float $min
     * @param float $max
     */
    public function setLoadAverageThresholds($min, $max)
    {
        $this->loadAverageThreshold = [(float) $min, (float) $max];
    }

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
     * Check if an alert needs to be fired
     *
     * @param ProvisionAlertEvent $event
     */
    public function handle(ProvisionAlertEvent $event)
    {
        $leftOver = $event->getLeftOver();
        $cpuUsage = $this->server->getCpuUsage();
        $loadAverage = $this->server->getCurrentLoadAverage();

        if ($leftOver === 0) {
            if (
                $cpuUsage <= $this->cpuThreshold[0] ||
                $loadAverage <= $this->loadAverageThreshold[0]
            ) {
                $type = AlerterInterface::UNDER_USED;
            }
        } else if ($leftOver > 0) {
            if (
                $cpuUsage >= $this->cpuThreshold[1] ||
                $loadAverage >= $this->loadAverageThreshold[1]
            ) {
                $type = AlerterInterface::OVER_USED;
            }
        }

        if (!isset($type)) {
            return;
        }

        foreach ($this->alerters as $alerter) {
            $alerter->alert(
                $type,
                $event->getCommandName(),
                $event->getCommandArguments(),
                $cpuUsage,
                $loadAverage,
                $leftOver
            );
        }
    }
}
