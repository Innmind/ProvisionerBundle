<?php

namespace Innmind\ProvisionerBundle\Tests\EventListener;

use Innmind\ProvisionerBundle\EventListener\RabbitMQRequirementListener;
use Innmind\ProvisionerBundle\ProcessStatusHandler;
use Innmind\ProvisionerBundle\RabbitMQ\QueueHistory;
use Innmind\ProvisionerBundle\RabbitMQ\Admin;
use Innmind\ProvisionerBundle\Event\ProvisionRequirementEvent;
use Innmind\ProvisionerBundle\Server\DummyServer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RabbitMQRequirementListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;

    public function setUp()
    {
        $handler = new ProcessStatusHandler();
        $handler->setServer(new DummyServer());
        $handler->setUsePrecision(false);

        $history = new QueueHistory();
        $history->setStoreDirectory('/tmp/innmind_provisioner');
        $history->setFilesystem(new Filesystem());
        $history->setFinder(new Finder());

        $admin = $this
            ->getMockBuilder('Innmind\\ProvisionerBundle\\RabbitMQ\\Admin')
            ->getMock();
        $admin->method('listQueueMessages')->willReturn(4000);

        $this->listener = new RabbitMQRequirementListener();
        $this->listener->setProcessStatusHandler($handler);
        $this->listener->setQueueHistory($history);
        $this->listener->setRabbitMQAdmin($admin);
    }

    public function testDoesntHandleEvent()
    {
        $event = new ProvisionRequirementEvent('vendor:command:test', new ArrayInput([]));

        $this->listener->handle($event);

        $this->assertEquals(0, $event->getRequiredProcesses());
        $this->assertFalse($event->isPropagationStopped());
    }

    public function testSetProvisionRequirement()
    {
        $event = new ProvisionRequirementEvent(
            'rabbitmq:consumer',
            new ArrayInput(
                [
                    'name' => 'foo',
                    '--messages' => '50'
                ],
                new InputDefinition([
                    new InputArgument('name', InputArgument::REQUIRED),
                    new InputOption('messages', 'm', InputOption::VALUE_OPTIONAL),
                ])
            )
        );

        $this->listener->handle($event);

        $this->assertTrue($event->getRequiredProcesses() > 0);
        $this->assertTrue($event->isPropagationStopped());
    }

    public function testGetEstimatedDepth()
    {
        $estimated = $this->listener->getEstimatedDepth([
            4000,
            1000,
            3000,
            2000
        ]);

        $this->assertEquals(
            ['previous' => 2300, 'current' => 1900],
            $estimated
        );

        $estimated = $this->listener->getEstimatedDepth([
            200,
            1000,
            1200,
            2000
        ]);

        $this->assertEquals(
            ['previous' => 1380, 'current' => 1940],
            $estimated
        );
    }

    public function testComputeRequirement()
    {
        $required = $this->listener->computeRequirement(
            0,
            40,
            50,
            1
        );
        $this->assertEquals(0, $required);

        $required = $this->listener->computeRequirement(
            0,
            100,
            50,
            1
        );
        $this->assertEquals(2, $required);

        $required = $this->listener->computeRequirement(
            200,
            40,
            50,
            2
        );
        $this->assertEquals(0, $required);

        $required = $this->listener->computeRequirement(
            200,
            140,
            50,
            2
        );
        $this->assertEquals(1, $required);

        $required = $this->listener->computeRequirement(
            200,
            400,
            50,
            2
        );
        $this->assertEquals(4, $required);

        $required = $this->listener->computeRequirement(
            200,
            200,
            50,
            2
        );
        $this->assertEquals(2, $required);
    }
}
