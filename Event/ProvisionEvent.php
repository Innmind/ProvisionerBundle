<?php

namespace Innmind\ProvisionerBundle\Event;

/**
 * Event fired to instruct to run the given number of processes
 */
class ProvisionEvent extends AbstractEvent
{
    protected $count = 0;

    /**
     * Constructor
     *
     * @param string $name
     * @param array  $args
     * @param int $count
     */
    public function __construct($name, array $args, $count)
    {
        parent::__construct($name, $args);

        $this->count = (int) $count;
    }

    /**
     * Return the number of processes to start
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}
