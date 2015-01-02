<?php

namespace Innmind\ProvisionerBundle\Tests\DependencyInjection\Compiler;

use Innmind\ProvisionerBundle\DependencyInjection\Compiler\RegisterAlertersPass;
use Innmind\ProvisionerBundle\DependencyInjection\InnmindProvisionerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterAlertersPassTest extends \PHPUnit_Framework_TestCase
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

        $definition = new Definition('stdClass');
        $definition->addTag('innmind_provisioner.alerter');
        $this->container->setDefinition('random.service', $definition);
    }

    public function testSetConsumerDefinitions()
    {
        $pass = new RegisterAlertersPass();
        $pass->process($this->container);

        $def = $this->container->getDefinition('innmind_provisioner.listener.alert');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'addAlerter';
        });
        $calls = array_values($calls);

        $this->assertEquals(1, count($calls));
        $this->assertEquals(
            'random.service',
            (string) $calls[0][1][0]
        );
    }
}
