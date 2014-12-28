<?php

namespace Innmind\ProvisionerBundle\Server;

use Symfony\Component\Process\Process;

/**
 * Helper to retrieve information about the server usage (cpu and load average)
 */
class Server implements ServerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCpuUsage()
    {
        $usage = 0;

        foreach ($this->getProcesses() as $process) {
            $usage += $process['usage'];
        }

        return $usage;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcesses()
    {
        $processes = [];
        $process = new Process('ps -eo pcpu,command');
        $process->run();
        $output = $process->getOutput();
        $output = explode("\n", $output);

        foreach ($output as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            list($cpu, $process) = explode(' ', $line, 2);

            if (is_numeric($cpu)) {
                $processes[] = [
                    'usage' => (float) $cpu,
                    'name' => $process
                ];
            }
        }

        return $processes;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessUsage($processName)
    {
        $usage = 0;

        foreach ($this->getProcesses() as $process) {
            if (strpos($process['name'], $processName) !== false) {
                $usage += $process['usage'];
            }
        }

        return $usage;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessCount($command)
    {
        $count = 0;

        foreach ($this->getProcesses() as $process) {
            if (strpos($process['name'], $command) !== false) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoadAverage()
    {
        return sys_getloadavg();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLoadAverage()
    {
        return $this->getLoadAverage()[0];
    }
}
