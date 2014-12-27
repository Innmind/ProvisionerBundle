<?php

namespace Innmind\ProvisionerBundle\Tests;

use Innmind\ProvisionerBundle\CommandHelper;

class CommandHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testGetArgumentsAsString()
    {
        $string = CommandHelper::getArgumentsAsString([
            'foo' => 'bar',
            'foo',
            'baz' => [
                'foo',
                'bar',
                'baz'
            ]
        ]);

        $this->assertEquals(
            '--foo=bar foo --baz=foo --baz=bar --baz=baz',
            $string
        );
    }
}
