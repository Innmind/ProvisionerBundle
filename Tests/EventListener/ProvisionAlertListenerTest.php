<?php

namespace Innmind\ProvisionerBundle\Tests\EventListener;

use Innmind\ProvisionerBundle\EventListener\ProvisionAlertListener;
use Innmind\ProvisionerBundle\Event\ProvisionAlertEvent;
use Innmind\ProvisionerBundle\Alert\AlerterInterface;
use Innmind\ProvisionerBundle\Alert\Alert;
use Innmind\ProvisionerBundle\ProcessStatusHandler;
use Innmind\ProvisionerBundle\Server\DummyServer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class ProvisionAlertListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;
    protected $alerter;

    public function setUp()
    {
        $handler = new ProcessStatusHandler();
        $handler->setServer(new DummyServer());
        $handler->setUsePrecision(false);
        $this->listener = new ProvisionAlertListener();
        $this->listener->setCpuThresholds(10, 100);
        $this->listener->setLoadAverageThresholds(0.2, 4);
        $this->listener->setProcessStatusHandler($handler);
        $this->alerter = new FakeAlerter();
        $this->listener->addAlerter($this->alerter);
        $this->listener->addAlerter($this->alerter);
    }

    protected function assert($type, $cpu, $load, $leftOver)
    {
        $server = $this
            ->getMockBuilder('Innmind\\ProvisionerBundle\\Server\\Server')
            ->getMock();
        $server->method('getCpuUsage')->willReturn($cpu);
        $server->method('getCurrentLoadAverage')->willReturn($load);
        $this->listener->setServer($server);
        $input = new ArrayInput([]);

        $event = new ProvisionAlertEvent('foo', $input, $leftOver);
        $this->listener->handle($event);

        $this->assertEquals(
            $type !== null ?
                [$type, 'foo', $input, $cpu, $load, $leftOver, 10] :
                null,
            $this->alerter->getData()
        );
    }

    public function testAlertCpuUnderUsed()
    {
        $this->assert(Alert::UNDER_USED, 5, 1, 0);
    }

    public function testAlertLoadAverageUnderUsed()
    {
        $this->assert(Alert::UNDER_USED, 20, 0.1, 0);
    }

    public function testAlertCpuOverUsed()
    {
        $this->assert(Alert::OVER_USED, 200, 2, 10);
    }

    public function testAlertLoadAverageOverUsed()
    {
        $this->assert(Alert::OVER_USED, 80, 5, 10);
    }

    public function testNoAlert()
    {
        $this->assert(null, 50, 2, 0);
    }

    public function testNoAlertEvenIfLeftOverButUnderThreshold()
    {
        $this->assert(null, 50, 2, 10);
    }
}

class FakeAlerter implements AlerterInterface
{
    protected $data;

    public function alert(Alert $alert)
    {
        $this->data = [
            $alert->getType(),
            $alert->getCommandName(),
            $alert->getCommandInput(),
            $alert->getCpuUsage(),
            $alert->getLoadAverage(),
            $alert->getLeftOver(),
            $alert->getRunningProcesses(),
        ];
    }

    public function getData()
    {
        $data = $this->data;
        $this->data = null;
        return $data;
    }
}
