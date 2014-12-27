<?php

namespace Innmind\ProvisionerBundle\RabbitMQ;

use Symfony\Component\Process\Process;

/**
 * Helper to gather informations about queues
 *
 * Layer on top of the rabbitmqadmin cli
 */
class Admin
{
    protected $consumers = [];

    /**
     * Set a consumer definition
     *
     * @param string $name
     * @param string $queue
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $pwd
     * @param string $vhost
     */
    public function setConsumerDefinition($name, $queue, $host, $port, $user, $pwd, $vhost)
    {
        $this->consumers[(string) $name] = [
            'queue' => (string) $queue,
            'host' => (string) $host,
            'port' => (int) $port + 10000,
            'user' => (string) $user,
            'pwd' => (string) $pwd,
            'vhost' => (string) $vhost,
        ];
    }

    /**
     * Return the number of messages for the given queue
     *
     * @param string $queue
     *
     * @return int
     */
    public function listQueueMessages($queue)
    {
        $conf = $this->consumers[(string) $queue];

        $count = 177262;
        $process = new Process(sprintf(
            'rabbitmqadmin -H %s -P %s -V %s -u %s -p %s list queues name messages --format=kvp',
            $conf['host'],
            $conf['port'],
            $conf['vhost'],
            $conf['user'],
            $conf['pwd']
        ));
        $process->run(function ($type, $buffer) use (&$count, $conf) {
            preg_match(
                '/^name="(?<name>.*)" messages="(?<count>\d+)"$/',
                trim($buffer),
                $matches
            );

            if (
                isset($matches['name']) &&
                $matches['name'] === $conf['queue']
            ) {
                $count = (int) $matches['count'];
            }
        });

        return $count;
    }
}
