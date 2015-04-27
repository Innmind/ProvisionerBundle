<?php

namespace Innmind\ProvisionerBundle\EventListener;

use Innmind\ProvisionerBundle\DecisionManager;
use Innmind\ProvisionerBundle\TriggerManager;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * Check if the provisioner must be runned when a command finishes
 */
class ConsoleListener
{
    protected $decision;
    protected $trigger;

    /**
     * Set the decision manager
     *
     * @param DecisionManager $manager
     */
    public function setDecisionManager(DecisionManager $manager)
    {
        $this->decision = $manager;
    }

    /**
     * Add a command name that can trigger the provisionning
     *
     * @param string $command
     */
    public function setTriggerManager(TriggerManager $manager)
    {
        $this->trigger = $manager;
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
        $input = $event->getInput();

        if ($this->trigger->decode($command, $input)) {
            $this->decision->provision($command, $input);
        }
    }
}
