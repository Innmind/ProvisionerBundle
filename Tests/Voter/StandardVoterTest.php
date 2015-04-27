<?php

namespace Innmind\ProvisionnerBundle\Tests\Voter;

use Innmind\ProvisionerBundle\Voter\StandardVoter;
use Symfony\Component\Console\Input\ArrayInput;

class StandardVoterTests extends \PHPUnit_Framework_TestCase
{
    public function testSupportsCommand()
    {
        $voter = new StandardVoter(['foo']);

        $this->assertTrue($voter->supportsCommand('foo'));
    }

    public function testDoesNotSupportsCommand()
    {
        $voter = new StandardVoter([]);

        $this->assertFalse($voter->supportsCommand('foo'));
    }

    public function testGrant()
    {
        $voter = new StandardVoter(['foo']);

        $this->assertEquals(
            StandardVoter::TRIGGER_GRANTED,
            $voter->vote('foo', new ArrayInput([]))
        );
    }

    public function testAbstain()
    {
        $voter = new StandardVoter(['foo']);

        $this->assertEquals(
            StandardVoter::TRIGGER_ABSTAIN,
            $voter->vote('bar', new ArrayInput([]))
        );
    }
}
