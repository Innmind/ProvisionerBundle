<?php

namespace Innmind\ProvisionerBundle\Event;

use Symfony\Component\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    protected $commandName;
    protected $args;

    /**
     * Construct an event for the given command
     * @param string $name Command name
     * @param array  $args Command arguments
     */
    public function __construct($name, array $args)
    {
        $this->commandName = (string) $name;
        $this->args = $args;
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
     * Return the command arguments
     *
     * @return array
     */
    public function getCommandArguments()
    {
        return $this->args;
    }
}
