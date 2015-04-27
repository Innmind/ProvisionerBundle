<?php

namespace Innmind\ProvisionerBundle\Voter;

use Symfony\Component\Console\Input\InputInterface;

class StandardVoter implements VoterInterface
{
    protected $triggers;

    public function __construct(array $triggers)
    {
        $this->triggers = $triggers;
    }

    /**
     * @inheritdoc
     */
    public function supportsCommand($command)
    {
        return in_array($command, $this->triggers, true);
    }

    /**
     * @inheritdoc
     */
    public function vote($command, InputInterface $input)
    {
        return in_array($command, $this->triggers, true) ?
            self::TRIGGER_GRANTED :
            self::TRIGGER_ABSTAIN;
    }
}
