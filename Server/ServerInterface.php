<?php

namespace Innmind\ProvisionerBundle\Server;

interface ServerInterface
{
    /**
     * Get the whole CPU usage
     *
     * @return float
     */
    public function getCpuUsage();

    /**
     * Get all the running processes with their associated CPU usage
     *
     * @return array
     */
    public function getProcesses();

    /**
     * Get the CPU usage taken by a process (it can match on part of the process name)
     *
     * @param string $processName
     *
     * @return float
     */
    public function getProcessUsage($processName);

    /**
     * Return the number of process launched for the given command
     *
     * @param string $command
     *
     * @return int
     */
    public function getProcessCount($command);

    /**
     * Return the last 3 load averages
     *
     * @return array
     */
    public function getLoadAverage();

    /**
     * Return the current load average
     *
     * @return float
     */
    public function getCurrentLoadAverage();
}
