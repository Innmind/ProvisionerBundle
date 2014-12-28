<?php

namespace Innmind\ProvisionerBundle\Event;

/**
 * Event fired to give the chance to a listener to determine how many
 * processes should be started
 */
class ProvisionRequirementEvent extends AbstractEvent
{
    protected $required = 0;

    /**
     * Set the number of processes to start
     *
     * @param int $number
     */
    public function setRequiredProcesses($number)
    {
        $this->required = (int) $number;
    }

    /**
     * Return the number of required processes
     *
     * @return int
     */
    public function getRequiredProcesses()
    {
        return $this->required;
    }
}
