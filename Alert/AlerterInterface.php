<?php

namespace Innmind\ProvisionerBundle\Alert;

/**
 * Interface for any class that need to alert that the server is under/over used
 */
interface AlerterInterface
{
    const UNDER_USED = 'under_used';
    const OVER_USED = 'over_used';

    /**
     * Alert that the server could be taken down as it does do much work anymore
     *
     * @param string $type
     * @param string $name Command name
     * @param array $args Command arguments
     * @param float $cpuUsage
     * @param float $loadAverage
     * @param int $leftOver Number of processes that couldn't be run on the server
     */
    public function alert($type, $name, array $args, $cpuUsage, $loadAverage, $leftOver = 0);
}
