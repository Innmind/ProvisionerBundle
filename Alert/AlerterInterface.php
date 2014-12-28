<?php

namespace Innmind\ProvisionerBundle\Alert;

use Symfony\Component\Console\Input\InputInterface;

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
     * @param InputInterface $input Command input
     * @param float $cpuUsage
     * @param float $loadAverage
     * @param int $leftOver Number of processes that couldn't be run on the server
     */
    public function alert($type, $name, InputInterface $input, $cpuUsage, $loadAverage, $leftOver = 0);
}
