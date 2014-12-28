<?php

namespace Innmind\ProvisionerBundle\Event;

use Symfony\Component\Console\Input\InputInterface;

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
     * @param InputInterface $input
     * @param int $leftOver
     */
    public function __construct($name, InputInterface $input, $leftOver)
    {
        parent::__construct($name, $input);

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
