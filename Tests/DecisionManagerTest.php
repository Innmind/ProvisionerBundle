<?php

namespace Innmind\ProvisionerBundle;

use Innmind\ProvisionerBundle\DecisionManager;
use Innmind\ProvisionerBundle\ProcessStatusHandler;
use Innmind\ProvisionerBundle\Server\DummyServer;

class DecisionManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;

    public function setUp()
    {
        $handler = new ProcessStatusHandler();
        $handler->setServer(new DummyServer());
        $handler->setUsePrecision(false);

        $this->manager = new DecisionManager();
        $this->manager->setServer(new DummyServer());
        $this->manager->setCpuThreshold(100);
        $this->manager->setProcessStatusHandler($handler);
    }

    public function testGetAllowedProcesses()
    {
        $allowed = $this->manager->getAllowedProcesses('whatever');

        $this->assertTrue(is_int($allowed));
        $this->assertEquals(8, $allowed); //8 is based on the numbers given by the dummy server
    }
}
