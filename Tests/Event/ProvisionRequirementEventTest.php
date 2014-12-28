<?php

namespace Innmind\ProvisionerBundle\Tests\Event;

use Innmind\ProvisionerBundle\Event\ProvisionRequirementEvent;
use Symfony\Component\Console\Input\ArrayInput;

class ProvisionRequirementEventTest extends \PHPUnit_Framework_TestCase
{
    public function testSetRequiredProcesses()
    {
        $event = new ProvisionRequirementEvent('foo', new ArrayInput([]));
        $event->setRequiredProcesses('42');

        $this->assertEquals(42, $event->getRequiredProcesses());
    }
}
