<?php

namespace Innmind\ProvisionerBundle\Listener;

use Innmind\ProvisionerBundle\DecisionManager;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * Check if the provisioner must be runned when a command finishes
 */
class ConsoleListener
{
    protected $manager;
    protected $triggers = [];

    /**
     * Set the decision manager
     *
     * @param DecisionManager $manager
     */
    public function setManager(DecisionManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Add a command name that can trigger the provisionning
     *
     * @param string $command
     */
    public function addTrigger($command)
    {
        $this->triggers[] = (string) $command;
    }

    /**
     * Handle event
     *
     * @param ConsoleTerminateEvent $event
     */
    public function handle(ConsoleTerminateEvent $event)
    {
        if ($event->getExitCode() !== 0) {
            //do not try to provision failling commands
            return;
        }

        $command = $event->getCommand()->getName();

        if (in_array($command, $this->triggers, true)) {
            $this->manager->provision(
                $command,
                $event->getInput()
            );
        }
    }
}
