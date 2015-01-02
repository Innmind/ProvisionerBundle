<?php

namespace Innmind\ProvisionerBundle\EventListener;

use Innmind\ProvisionerBundle\Event\ProvisionAlertEvent;
use Innmind\ProvisionerBundle\Alert\AlerterInterface;
use Innmind\ProvisionerBundle\Alert\Alert;
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

        $alert = new Alert();
        $alert
            ->setCommandName(
                $event->getCommandName()
            )
            ->setCommandInput(
                $event->getCommandInput()
            )
            ->setCpuUsage($cpuUsage)
            ->setLoadAverage($loadAverage)
            ->setLeftOver($leftOver);

        if ($leftOver === 0) {
            if (
                $cpuUsage <= $this->cpuThreshold[0] ||
                $loadAverage <= $this->loadAverageThreshold[0]
            ) {
                $alert->setUnderUsed();
            }
        } else if ($leftOver > 0) {
            if (
                $cpuUsage >= $this->cpuThreshold[1] ||
                $loadAverage >= $this->loadAverageThreshold[1]
            ) {
                $alert->setOverUsed();
            }
        }

        if (!$alert->isUnderUsed() && !$alert->isOverUsed()) {
            return;
        }

        foreach ($this->alerters as $alerter) {
            $alerter->alert($alert);
        }
    }
}
