<?php

namespace Innmind\ProvisionerBundle\RabbitMQ;

/**
 * Interface to push key/value pairs of data related to rabbit mq
 */
interface HistoryInterface
{
    /**
     * Set the value for a key
     *
     * @param string $key
     * @param array $value
     */
    public function put($key, array $value);

    /**
     * Return the value for the given key
     *
     * @param string $key
     *
     * @return array
     */
    public function get($key);
}
