<?php

namespace Innmind\ProvisionerBundle\Tests\DependencyInjection\Compiler;

use Innmind\ProvisionerBundle\DependencyInjection\Compiler\RegisterVotersPass;
use Innmind\ProvisionerBundle\DependencyInjection\InnmindProvisionerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterVotersPassTest extends \PHPUnit_Framework_TestCase
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
        $definition->addTag('innmind_provisioner.voter');
        $this->container->setDefinition('random.service', $definition);
    }

    public function testSetVoters()
    {
        $pass = new RegisterVotersPass();
        $pass->process($this->container);

        $def = $this->container->getDefinition('innmind_provisioner.trigger_manager');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'addVoter';
        });
        $calls = array_values($calls);

        $this->assertEquals(2, count($calls));
        $this->assertEquals(
            'random.service',
            (string) $calls[1][1][0]
        );
    }
}
