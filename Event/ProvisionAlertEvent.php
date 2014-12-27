<?php

namespace Innmind\ProvisionerBundle\Event;

/**
 * Event fired when the provisioner end its job giving access
 * to how many processes couldn't be launched
 */
class ProvisionAlertEvent extends AbstractEvent
{
    protected $leftOver = 0;

    /**
     * Constructor
     *
     * @param string $name
     * @param array  $args
     * @param int $leftOver
     */
    public function __construct($name, array $args, $leftOver)
    {
        parent::__construct($name, $args);

        $this->leftOver = (int) $leftOver;
    }

    /**
     * Return the number of processes that couldn't be launched
     *
     * @return int
     */
    public function getLeftOver()
    {
        return $this->leftOver;
    }
}
