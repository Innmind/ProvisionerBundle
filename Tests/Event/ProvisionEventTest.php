<?php

namespace Innmind\ProvisionerBundle\Tests\Event;

use Innmind\ProvisionerBundle\Event\ProvisionEvent;

class ProvisionEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCount()
    {
        $event = new ProvisionEvent('foo', [], 42);

        $this->assertEquals(42, $event->getCount());
    }
}
