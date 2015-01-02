<?php

namespace Innmind\ProvisionerBundle\Tests\DependencyInjection;

use Innmind\ProvisionerBundle\DependencyInjection\InnmindProvisionerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class InnmindProvisionerExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $extension;
    protected $config;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new InnmindProvisionerExtension();
        $this->config = [
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
        $this->extension->load($this->config, $this->container);
    }

    public function testSetBundleConfiguration()
    {
        $this->assertTrue($this->container->hasParameter('innmind_provisioner'));
        $this->assertEquals(
            $this->config['innmind_provisioner'],
            $this->container->getParameter('innmind_provisioner')
        );
    }

    public function testAddTriggers()
    {
        $def = $this->container->getDefinition('innmind_provisioner.listener.console');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'addTrigger';
        });
        $calls = array_values($calls);

        $this->assertEquals(1, count($calls));
        $this->assertEquals(['rabbitmq:consumer'], $calls[0][1]);
    }

    public function testSetMaxCpuThresholdOnDecisionManager()
    {
        $def = $this->container->getDefinition('innmind_provisioner.decision_manager');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'setCpuThreshold';
        });
        $calls = array_values($calls);

        $this->assertEquals(1, count($calls));
        $this->assertEquals([100], $calls[0][1]);
    }

    public function testSetRabbitMQHistoryLength()
    {
        $def = $this->container->getDefinition('innmind_provisioner.rabbitmq.queue_history');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'setHistoryLength';
        });
        $calls = array_values($calls);

        $this->assertEquals(1, count($calls));
        $this->assertEquals([1], $calls[0][1]);
    }

    public function testSetAllThresholdsOnAlertListener()
    {
        $def = $this->container->getDefinition('innmind_provisioner.listener.alert');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'setCpuThresholds';
        });
        $calls = array_values($calls);

        $this->assertEquals(1, count($calls));
        $this->assertEquals([10, 100], $calls[0][1]);

        $def = $this->container->getDefinition('innmind_provisioner.listener.alert');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'setLoadAverageThresholds';
        });
        $calls = array_values($calls);

        $this->assertEquals(1, count($calls));
        $this->assertEquals([0, 100], $calls[0][1]);
    }

    public function testSetEmailAlerting()
    {
        $def = $this->container->getDefinition('innmind_provisioner.listener.alert');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'addAlerter';
        });

        $this->assertEquals(0, count($calls));

        $conf = $this->config;
        $conf['innmind_provisioner']['alerting'] = [
            'email' => 'foo@bar.baz'
        ];

        $this->extension->load($conf, $this->container);

        $def = $this->container->getDefinition('innmind_provisioner.listener.alert');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'addAlerter';
        });
        $calls = array_values($calls);

        $this->assertEquals(1, count($calls));
        $this->assertEquals(
            'innmind_provisioner.alerter.email',
            (string) $calls[0][1][0]
        );
    }

    public function testSetWebhookAlerting()
    {
        $def = $this->container->getDefinition('innmind_provisioner.listener.alert');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'addAlerter';
        });

        $this->assertEquals(0, count($calls));

        $conf = $this->config;
        $conf['innmind_provisioner']['alerting'] = [
            'webhook' => ['http://localhost/webhook']
        ];

        $this->extension->load($conf, $this->container);

        $def = $this->container->getDefinition('innmind_provisioner.listener.alert');
        $calls = array_filter($def->getMethodCalls(), function ($el) {
            return $el[0] === 'addAlerter';
        });
        $calls = array_values($calls);

        $this->assertEquals(1, count($calls));
        $this->assertEquals(
            'innmind_provisioner.alerter.webhook',
            (string) $calls[0][1][0]
        );
    }
}
