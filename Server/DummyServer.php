<?php

namespace Innmind\ProvisionerBundle\Server;

/**
 * Helper used for tests purposes
 */
class DummyServer implements ServerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCpuUsage()
    {
        return 80;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcesses()
    {
        return [[
            'usage' => 42,
            'name' => 'phpunit'
        ]];
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessUsage($processName)
    {
        return 24;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessCount($command)
    {
        return 10;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoadAverage()
    {
        return [3, 2, 1];
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLoadAverage()
    {
        return 3;
    }
}
