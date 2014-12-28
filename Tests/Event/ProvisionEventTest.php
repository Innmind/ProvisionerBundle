<?php

namespace Innmind\ProvisionerBundle\Tests\Event;

use Innmind\ProvisionerBundle\Event\ProvisionEvent;
use Symfony\Component\Console\Input\ArrayInput;

class ProvisionEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCount()
    {
        $event = new ProvisionEvent('foo', new ArrayInput([]), 42);

        $this->assertEquals(42, $event->getCount());
    }
}
