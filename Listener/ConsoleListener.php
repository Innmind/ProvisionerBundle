<?php

namespace Innmind\ProvisionerBundle\Listener;

use Innmind\ProvisionerBundle\DecisionManager;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArgvInput;
use ReflectionObject;

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
                $this->reduce($event->getInput())
            );
        }
    }

    /**
     * Return input arguments and options merged in a single
     * array, also remove empty values
     *
     * @param ArgvInput $input
     *
     * @return array
     */
    public function reduce(ArgvInput $input)
    {
        $refl = new ReflectionObject($input);
        $reflToken = $refl->getProperty('tokens');
        $reflToken->setAccessible(true);
        $tokens = $reflToken->getValue($input);
        $reflToken->setAccessible(false);

        $args = $input->getArguments();
        unset($args['command']);
        $args = array_values($args);
        $options = $input->getOptions();
        $arguments = [];

        //make sure to have the arguments in the same order as inputed by user
        for ($i = 0, $l = count($tokens); $i < $l; $i++) {
            if (in_array($tokens[$i], $args, true)) {
                $arguments[] = $tokens[$i];
            }
        }

        foreach ($options as $key => $option) {
            if (empty($option)) {
                unset($options[$key]);
            }
        }

        return array_merge($arguments, $options);
    }
}
