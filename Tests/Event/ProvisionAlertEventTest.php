<?php

namespace Innmind\ProvisionerBundle\Tests\Event;

use Innmind\ProvisionerBundle\Event\ProvisionAlertEvent;

class ProvisionAlertEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLeftOver()
    {
        $event = new ProvisionAlertEvent('foo', [], 42);

        $this->assertEquals(42, $event->getLeftOver());
    }
}
