<?php

namespace Innmind\ProvisionerBundle;

use Innmind\ProvisionerBundle\ProcessStatusHandler;
use Innmind\ProvisionerBundle\Server\DummyServer;

class ProcessStatusHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $handler;

    public function setUp()
    {
        $this->handler = new ProcessStatusHandler();
        $this->handler->setServer(new DummyServer());
        $this->handler->setUsePrecision(false);
    }

    public function testGetProcessCount()
    {
        $count = $this->handler->getProcessCount('whatever');

        $this->assertTrue(is_int($count));
        $this->assertEquals(10, $count);
    }

    public function testGetProcessUsage()
    {
        $usage = $this->handler->getProcessUsage('whatever');

        $this->assertTrue(is_float($usage));
        $this->assertEquals(24, $usage);
    }
}
