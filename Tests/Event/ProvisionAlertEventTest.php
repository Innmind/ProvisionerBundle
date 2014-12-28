<?php

namespace Innmind\ProvisionerBundle\Tests\Event;

use Innmind\ProvisionerBundle\Event\ProvisionAlertEvent;
use Symfony\Component\Console\Input\ArrayInput;

class ProvisionAlertEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLeftOver()
    {
        $event = new ProvisionAlertEvent('foo', new ArrayInput([]), 42);

        $this->assertEquals(42, $event->getLeftOver());
    }
}
