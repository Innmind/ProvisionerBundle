<?php

namespace Innmind\ProvisionerBundle\Tests\Listener;

use Innmind\ProvisionerBundle\Listener\ProvisionAlertListener;
use Innmind\ProvisionerBundle\Event\ProvisionAlertEvent;
use Innmind\ProvisionerBundle\Alert\AlerterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class ProvisionAlertListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;
    protected $alerter;

    public function setUp()
    {
        $this->listener = new ProvisionAlertListener();
        $this->listener->setCpuThresholds(10, 100);
        $this->listener->setLoadAverageThresholds(0.2, 4);
        $this->alerter = new FakeAlerter();
        $this->listener->addAlerter($this->alerter);
        $this->listener->addAlerter($this->alerter);
    }

    public function testAlertCpuUnderUsed()
    {
        $server = $this
            ->getMockBuilder('Innmind\\ProvisionerBundle\\Server\\Server')
            ->getMock();
        $server->method('getCpuUsage')->willReturn(5);
        $server->method('getCurrentLoadAverage')->willReturn(1);
        $this->listener->setServer($server);
        $input = new ArrayInput([]);

        $event = new ProvisionAlertEvent('foo', $input, 0);
        $this->listener->handle($event);

        $this->assertEquals(
            [AlerterInterface::UNDER_USED, 'foo', $input, 5, 1, 0],
            $this->alerter->getData()
        );
    }

    public function testAlertLoadAverageUnderUsed()
    {
        $server = $this
            ->getMockBuilder('Innmind\\ProvisionerBundle\\Server\\Server')
            ->getMock();
        $server->method('getCpuUsage')->willReturn(20);
        $server->method('getCurrentLoadAverage')->willReturn(0.1);
        $this->listener->setServer($server);
        $input = new ArrayInput([]);

        $event = new ProvisionAlertEvent('foo', $input, 0);
        $this->listener->handle($event);

        $this->assertEquals(
            [AlerterInterface::UNDER_USED, 'foo', $input, 20, 0.1, 0],
            $this->alerter->getData()
        );
    }

    public function testAlertCpuOverUsed()
    {
        $server = $this
            ->getMockBuilder('Innmind\\ProvisionerBundle\\Server\\Server')
            ->getMock();
        $server->method('getCpuUsage')->willReturn(200);
        $server->method('getCurrentLoadAverage')->willReturn(2);
        $this->listener->setServer($server);
        $input = new ArrayInput([]);

        $event = new ProvisionAlertEvent('foo', $input, 10);
        $this->listener->handle($event);

        $this->assertEquals(
            [AlerterInterface::OVER_USED, 'foo', $input, 200, 2, 10],
            $this->alerter->getData()
        );
    }

    public function testAlertLoadAverageOverUsed()
    {
        $server = $this
            ->getMockBuilder('Innmind\\ProvisionerBundle\\Server\\Server')
            ->getMock();
        $server->method('getCpuUsage')->willReturn(80);
        $server->method('getCurrentLoadAverage')->willReturn(5);
        $this->listener->setServer($server);
        $input = new ArrayInput([]);

        $event = new ProvisionAlertEvent('foo', $input, 10);
        $this->listener->handle($event);

        $this->assertEquals(
            [AlerterInterface::OVER_USED, 'foo', $input, 80, 5, 10],
            $this->alerter->getData()
        );
    }

    public function testNoAlert()
    {
        $server = $this
            ->getMockBuilder('Innmind\\ProvisionerBundle\\Server\\Server')
            ->getMock();
        $server->method('getCpuUsage')->willReturn(50);
        $server->method('getCurrentLoadAverage')->willReturn(2);
        $this->listener->setServer($server);

        $event = new ProvisionAlertEvent('foo', new ArrayInput([]), 0);
        $this->listener->handle($event);

        $this->assertEquals(
            null,
            $this->alerter->getData()
        );
    }

    public function testNoAlertEvenIfLeftOverButUnderThreshold()
    {
        $server = $this
            ->getMockBuilder('Innmind\\ProvisionerBundle\\Server\\Server')
            ->getMock();
        $server->method('getCpuUsage')->willReturn(50);
        $server->method('getCurrentLoadAverage')->willReturn(2);
        $this->listener->setServer($server);

        $event = new ProvisionAlertEvent('foo', new ArrayInput([]), 10);
        $this->listener->handle($event);

        $this->assertEquals(
            null,
            $this->alerter->getData()
        );
    }
}

class FakeAlerter implements AlerterInterface
{
    protected $data;

    public function alert($type, $name, InputInterface $input, $cpuUsage, $loadAverage, $leftOver = 0)
    {
        $this->data = [$type, $name, $input, $cpuUsage, $loadAverage, $leftOver];
    }

    public function getData()
    {
        $data = $this->data;
        $this->data = null;
        return $data;
    }
}
