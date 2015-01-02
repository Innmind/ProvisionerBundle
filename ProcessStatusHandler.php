<?php

namespace Innmind\ProvisionerBundle;

use Innmind\ProvisionerBundle\Server\ServerInterface;

/**
 * Holds informations about a process througthout a symfony runtime
 */
class ProcessStatusHandler
{
    const COMPUTE_ITERATIONS = 3;
    const ITERATIONS_SLEEP = 2;

    protected $informations = [];
    protected $server;
    protected $usePrecision = true;

    /**
     * Set the server helper
     *
     * @param ServerInterface $server
     */
    public function setServer(ServerInterface $server)
    {
        $this->server = $server;
    }

    /**
     * Whether to run compute multiple times cpu usage
     * to get a better approximation or not
     *
     * @param bool $use
     */
    public function setUsePrecision($use)
    {
        $this->usePrecision = (bool) $use;
    }

    /**
     * Return the number of process running for the givent command
     *
     * @param string $command
     *
     * @return int
     */
    public function getProcessCount($command)
    {
        $hash = $this->getKey($command);

        if (!isset($this->informations[$hash])) {
            $this->computeProcessInformations($command);
        }

        return $this->informations[$hash]['number'];
    }

    /**
     * Return the average CPU usage for the given command
     *
     * @param string $command
     *
     * @return float
     */
    public function getProcessUsage($command)
    {
        $hash = $this->getKey($command);

        if (!isset($this->informations[$hash])) {
            $this->computeProcessInformations($command);
        }

        return $this->informations[$hash]['usage'];
    }

    /**
     * Compute the process usage/number for the given command
     *
     * @param string $command
     */
    protected function computeProcessInformations($command)
    {
        $averageProcessUsage = 0;
        $averageProcessNumber = 0;
        $iterations = $this->usePrecision ? self::COMPUTE_ITERATIONS : 1;

        for ($i = 0; $i < $iterations; $i++) {
            $averageProcessUsage += $this->server->getProcessUsage($command);
            $averageProcessNumber += $this->server->getProcessCount($command);

            if ($this->usePrecision === true) {
                sleep(self::ITERATIONS_SLEEP);
            }
        }

        $averageProcessUsage = $averageProcessUsage / $iterations;
        $averageProcessNumber = floor($averageProcessNumber / $iterations);

        $this->informations[$this->getKey($command)] = [
            'usage' => (float) $averageProcessUsage,
            'number' => (int) $averageProcessNumber
        ];
    }

    /**
     * Return a unique key for the given command
     *
     * @param string $command
     *
     * @return string
     */
    protected function getKey($command)
    {
        return md5($command);
    }
}
