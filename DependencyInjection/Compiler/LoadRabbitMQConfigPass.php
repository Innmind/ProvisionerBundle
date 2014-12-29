<?php

namespace Innmind\ProvisionerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Extract RabbitMQ config from "old sound" bundle services
 * and inject them in the rabbitmq admin object
 */
class LoadRabbitMQConfigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $admin = $container->getDefinition('innmind_provisioner.rabbitmq.admin');
        $consumers = $container->findTaggedServiceIds(
            'old_sound_rabbit_mq.consumer'
        );

        foreach ($consumers as $id => $tags) {
            $def = $container->getDefinition($id);

            foreach ($def->getMethodCalls() as $call) {
                if ($call[0] === 'setQueueOptions') {
                    $queue = $call[1][0]['name'];
                    break;
                }
            }

            if (!isset($queue)) {
                continue;
            }

            $connectionId = (string) $def->getArgument(0);
            $def = $container->getDefinition($connectionId);

            $host = $def->getArgument(0);
            $port = $def->getArgument(1);
            $user = $def->getArgument(2);
            $pwd = $def->getArgument(3);
            $vhost = $def->getArgument(4);

            $admin->addMethodCall(
                'setConsumerDefinition',
                [
                    substr($id, 20, -9),
                    $queue,
                    $host,
                    $port,
                    $user,
                    $pwd,
                    $vhost,
                ]
            );
        }
    }
}
