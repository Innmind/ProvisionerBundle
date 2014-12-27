<?php

namespace Innmind\ProvisionerBundle\Tests\Server;

use Innmind\ProvisionerBundle\Server\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCpuUsage()
    {
        $server = new Server();

        $this->assertTrue(is_float($server->getCpuUsage()));
        $this->assertTrue($server->getCpuUsage() > 0);
    }

    public function testGetProcesses()
    {
        $server = new Server();

        $this->assertTrue(is_array($server->getProcesses()));
        $this->assertTrue(isset($server->getProcesses()[0]['usage']));
        $this->assertTrue(is_float($server->getProcesses()[0]['usage']));
        $this->assertTrue(isset($server->getProcesses()[0]['name']));
        $this->assertTrue(is_string($server->getProcesses()[0]['name']));
    }

    public function testGetProcessUsage()
    {
        $server = new Server();

        $this->assertTrue(is_float($server->getProcessUsage('phpunit')));
    }

    public function testGetProcessCount()
    {
        $server = new Server();

        $this->assertTrue(is_int($server->getProcessCount('phpunit')));
        $this->assertEquals(1, $server->getProcessCount('phpunit'));
    }

    public function testGetLoadAverage()
    {
        $server = new Server();

        $this->assertTrue(is_array($server->getLoadAverage()));
        $this->assertEquals(3, count($server->getLoadAverage()));
        $this->assertTrue(is_float($server->getLoadAverage()[0]));
        $this->assertTrue(is_float($server->getLoadAverage()[1]));
        $this->assertTrue(is_float($server->getLoadAverage()[2]));
    }

    public function testgetCurrentLoadAverage()
    {
        $server = new Server();

        $this->assertTrue(is_float($server->getCurrentLoadAverage()));
    }
}
