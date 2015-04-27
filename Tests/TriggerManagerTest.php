<?php

namespace Innmind\ProvisionerBundle\Tests;

use Innmind\ProvisionerBundle\TriggerManager;
use Innmind\ProvisionerBundle\Voter\VoterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class TriggerManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testAffirmativeStrategy()
    {
        $m = new TriggerManager();
        $input = new ArrayInput([]);
        $m
            ->addVoter(new GrantVoter())
            ->addVoter(new AbstainVoter());

        $this->assertTrue($m->decide('foo', $input));
    }

    public function testConsensusStrategy()
    {
        $m = new TriggerManager(TriggerManager::STRATEGY_CONSENSUS);
        $input = new ArrayInput([]);
        $m
            ->addVoter(new GrantVoter())
            ->addVoter(new AbstainVoter());

        $this->assertTrue($m->decide('foo', $input));
    }

    public function testConsensusStrategyWhenEqualGrantedDenied()
    {
        $m = new TriggerManager(TriggerManager::STRATEGY_CONSENSUS);
        $input = new ArrayInput([]);
        $m
            ->addVoter(new GrantVoter())
            ->addVoter(new DenyVoter());

        $this->assertTrue($m->decide('foo', $input));

        $m = new TriggerManager(TriggerManager::STRATEGY_CONSENSUS, false);
        $input = new ArrayInput([]);
        $m
            ->addVoter(new GrantVoter())
            ->addVoter(new DenyVoter());

        $this->assertFalse($m->decide('foo', $input));
    }

    public function testConsensusStrategyWhenAllAbstain()
    {
        $m = new TriggerManager(TriggerManager::STRATEGY_CONSENSUS);
        $input = new ArrayInput([]);
        $m
            ->addVoter(new AbstainVoter());

        $this->assertFalse($m->decide('foo', $input));

        $m = new TriggerManager(TriggerManager::STRATEGY_CONSENSUS, true, true);
        $input = new ArrayInput([]);
        $m
            ->addVoter(new AbstainVoter());

        $this->assertTrue($m->decide('foo', $input));
    }
}

class GrantVoter implements VoterInterface
{
    public function supportsCommand($command)
    {
        return true;
    }

    public function vote($command, InputInterface $input)
    {
        return VoterInterface::TRIGGER_GRANTED;
    }
}

class DenyVoter implements VoterInterface
{
    public function supportsCommand($command)
    {
        return true;
    }

    public function vote($command, InputInterface $input)
    {
        return VoterInterface::TRIGGER_DENIED;
    }
}

class AbstainVoter implements VoterInterface
{
    public function supportsCommand($command)
    {
        return true;
    }

    public function vote($command, InputInterface $input)
    {
        return VoterInterface::TRIGGER_ABSTAIN;
    }
}
