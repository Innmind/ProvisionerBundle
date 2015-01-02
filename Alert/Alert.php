<?php

namespace Innmind\ProvisionerBundle\Alert;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Hold any data relative to an alert
 */
class Alert
{
    const UNDER_USED = 'under_used';
    const OVER_USED = 'over_used';

    protected $type;
    protected $commandName;
    protected $commandInput;
    protected $cpuUsage;
    protected $loadAverage;
    protected $leftOver = 0;

    /**
     * Set the alert type to under used
     *
     * @return Alert self
     */
    public function setUnderUsed()
    {
        $this->type = self::UNDER_USED;

        return $this;
    }

    /**
     * Check if the alert is of under used type
     *
     * @return bool
     */
    public function isUnderUsed()
    {
        return $this->type === self::UNDER_USED;
    }

    /**
     * Set the alert type to over used
     *
     * @return Alert self
     */
    public function setOverUsed()
    {
        $this->type = self::OVER_USED;

        return $this;
    }

    /**
     * Check if the alert is of over used type
     *
     * @return bool
     */
    public function isOverUsed()
    {
        return $this->type === self::OVER_USED;
    }

    /**
     * Return the alert type as string
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the command name
     *
     * @param string $name
     *
     * @return Alert self
     */
    public function setCommandName($name)
    {
        $this->commandName = (string) $name;

        return $this;
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
     * Set the command input
     *
     * @param InputInterface $input
     *
     * @return Alert self
     */
    public function setCommandInput(InputInterface $input)
    {
        $this->commandInput = $input;

        return $this;
    }

    /**
     * Return the command input
     *
     * @return InputInterface
     */
    public function getCommandInput()
    {
        return $this->commandInput;
    }

    /**
     * Set the server CPU usage at provision time
     *
     * @param float $usage
     *
     * @return Alert self
     */
    public function setCpuUsage($usage)
    {
        $this->cpuUsage = (float) $usage;

        return $this;
    }

    /**
     * Return the CPU usage
     *
     * @return float
     */
    public function getCpuUsage()
    {
        return $this->cpuUsage;
    }

    /**
     * Set the server load average at provision time
     *
     * @param float $load
     *
     * @return Alert self
     */
    public function setLoadAverage($load)
    {
        $this->loadAverage = (float) $load;

        return $this;
    }

    /**
     * Return the load average
     *
     * @return float
     */
    public function getLoadAverage()
    {
        return $this->loadAverage;
    }

    /**
     * Set the number of processes that couldn't be launched
     *
     * @param int $leftOver
     *
     * @return Alert self
     */
    public function setLeftOver($leftOver)
    {
        $this->leftOver = (int) $leftOver;

        return $this;
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
