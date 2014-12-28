<?php

namespace Innmind\ProvisionerBundle\Event;

use Symfony\Component\Console\Input\InputInterface;

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
     * @param InputInterface $input
     * @param int $count
     */
    public function __construct($name, InputInterface $input, $count)
    {
        parent::__construct($name, $input);

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
