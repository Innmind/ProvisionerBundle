<?php

namespace Innmind\ProvisionerBundle\Tests\DependencyInjection\Compiler;

use Innmind\ProvisionerBundle\DependencyInjection\Compiler\LoadRabbitMQConfigPass;
use Innmind\ProvisionerBundle\DependencyInjection\InnmindProvisionerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LoadRabbitMQConfigPassTest extends \PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $extension = new InnmindProvisionerExtension();
        $config = [
            'innmind_provisioner' => [
                'threshold' => [
                    'cpu' => [
                        'max' => 100,
                        'min' => 10,
                    ],
                    'load_average' => [
                        'max' => 100,
                        'min' => 0,
                    ]
                ],
                'triggers' => ['rabbitmq:consumer'],
                'rabbitmq' => [
                    'queue_depth' => [
                        'history_length' => 1,
                    ]
                ]
            ]
        ];
        $extension->load($config, $this->container);

        $definition = new Definition('stdClass', [new Reference('old_sound_rabbit_mq.connection.default')]);
        $definition->addMethodCall('setQueueOptions', [['name' => 'some.queue']]);
        $definition->addTag('old_sound_rabbit_mq.consumer');
        $this->container->setDefinition('old_sound_rabbit_mq.foo_consumer', $definition);

        $definition = new Definition('stdClass', [new Reference('old_sound_rabbit_mq.connection.default')]);
        $definition->addTag('old_sound_rabbit_mq.consumer');
        $this->container->setDefinition('old_sound_rabbit_mq.bar_consumer', $definition);

        $definition = new Definition('stdClass', [
            'localhost',
            '15672',
            'guest',
            'guest',
            '/'
        ]);
        $this->container->setDefinition('old_sound_rabbit_mq.connection.default', $definition);
    }

    public function testSetConsumerDefinitions()
    {
        $pass = new LoadRabbitMQConfigPass();
        $pass->process($this->container);

        $def = $this->container->getDefinition('innmind_provisioner.rabbitmq.admin');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'setConsumerDefinition';
        });
        $calls = array_values($calls);

        $this->assertEquals(1, count($calls));
        $this->assertEquals(
            ['foo', 'some.queue', 'localhost', 15672, 'guest', 'guest', '/'],
            $calls[0][1]
        );
    }
}
