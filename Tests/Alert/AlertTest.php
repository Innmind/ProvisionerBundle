<?php

namespace Innmind\ProvisionerBundle\Tests\Alert;

use Innmind\ProvisionerBundle\Alert\Alert;
use Symfony\Component\Console\Input\ArrayInput;

class AlertTest extends \PHPUnit_Framework_TestCase
{
    protected $alert;

    public function setUp()
    {
        $this->alert = new Alert();
    }

    public function testSetUnderUsed()
    {
        $this->assertEquals(
            $this->alert,
            $this->alert->setUnderUsed()
        );
        $this->assertEquals(
            Alert::UNDER_USED,
            $this->alert->getType()
        );
        $this->assertTrue($this->alert->isUnderUsed());
    }

    public function testSetOverUsed()
    {
        $this->assertEquals(
            $this->alert,
            $this->alert->setOverUsed()
        );
        $this->assertEquals(
            Alert::OVER_USED,
            $this->alert->getType()
        );
        $this->assertTrue($this->alert->isOverUsed());
    }

    public function testSetCommandName()
    {
        $this->assertEquals(
            $this->alert,
            $this->alert->setCommandName('foo')
        );
        $this->assertEquals(
            'foo',
            $this->alert->getCommandName()
        );
    }

    public function testSetCommandInput()
    {
        $input = new ArrayInput([]);
        $this->assertEquals(
            $this->alert,
            $this->alert->setCommandInput($input)
        );
        $this->assertEquals(
            $input,
            $this->alert->getCommandInput()
        );
    }

    public function testSetCpuUsage()
    {
        $this->assertEquals(
            $this->alert,
            $this->alert->setCpuUsage('40.2')
        );
        $this->assertEquals(
            40.2,
            $this->alert->getCpuUsage()
        );
    }

    public function testSetLoadAverage()
    {
        $this->assertEquals(
            $this->alert,
            $this->alert->setLoadAverage('2.4')
        );
        $this->assertEquals(
            2.4,
            $this->alert->getLoadAverage()
        );
    }

    public function testSetRunningProcesses()
    {
        $this->assertEquals(
            $this->alert,
            $this->alert->setRunningProcesses('42.0')
        );
        $this->assertEquals(
            42,
            $this->alert->getRunningProcesses()
        );
    }

    public function testSetLeftOver()
    {
        $this->assertEquals(
            $this->alert,
            $this->alert->setLeftOver('10.0')
        );
        $this->assertEquals(
            10,
            $this->alert->getLeftOver()
        );
    }
}
