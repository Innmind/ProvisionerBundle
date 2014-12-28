<?php

namespace Innmind\ProvisionerBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractEvent extends Event
{
    protected $commandName;
    protected $input;

    /**
     * Construct an event for the given command
     * @param string $name Command name
     * @param InputInterface $input Command input
     */
    public function __construct($name, InputInterface $input)
    {
        $this->commandName = (string) $name;
        $this->input = $input;
    }

    /**
     * Return the command name
     *
     * @return string
     */
    public function getCommandName()
    {
        return $this->commandName;
    }

    /**
     * Return the command input
     *
     * @return InputInterface
     */
    public function getCommandInput()
    {
        return $this->input;
    }
}
