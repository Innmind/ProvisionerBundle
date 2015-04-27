<?php

namespace Innmind\ProvisionerBundle;

use Innmind\ProvisionerBundle\Voter\VoterInterface;
use Symfony\Component\Console\Input\InputInterface;

class TriggerManager
{
    const STRATEGY_AFFIRMATIVE = 'affirmative';
    const STRATEGY_CONSENSUS = 'consensus';
    const STRATEGY_UNANIMOUS = 'unanimous';

    protected $voters = [];
    protected $strategy;
    protected $allowIfEqualGrantedDeniedDecisions;
    protected $allowIfAllAbstainDecisions;

    public function __construct($strategy = self::STRATEGY_AFFIRMATIVE, $allowIfEqualGrantedDeniedDecisions = true, $allowIfAllAbstainDecisions = false)
    {
        if (!defined(sprintf('self::STRATEGY_%s', strtoupper($strategy)))) {
            throw new \InvalidArgumentException(sprintf('The strategy "%s" is not supported', $strategy));
        }

        $this->strategy = $strategy;
        $this->allowIfEqualGrantedDeniedDecisions = $allowIfEqualGrantedDeniedDecisions;
        $this->allowIfAllAbstainDecisions = $allowIfAllAbstainDecisions;
    }

    /**
     * Add a new voter to the trigger manager
     *
     * @param VoterInterface $voter
     *
     * @return TriggerManager self
     */
    public function addVoter(VoterInterface $voter)
    {
        $this->voters[] = $voter;

        return $this;
    }

    /**
     * Decide if the decision manager should be triggered or not
     *
     * @param string $command
     * @param InputInterface $input
     *
     * @return bool
     */
    public function decide($command, InputInterface $input)
    {
        $grant = 0;
        $deny = 0;
        $abstain = 0;

        foreach ($this->voters as $voter) {
            if (!$voter->supportsCommand($command)) {
                continue;
            }

            $vote = $voter->vote($command, $input);

            switch ($vote) {
                case VoterInterface::TRIGGER_GRANTED:
                    $grant++;
                    break;
                case VoterInterface::TRIGGER_DENIED:
                    $deny++;
                    break;
                default:
                    $abstain++;
                    break;
            }
        }

        switch ($this->strategy) {
            case self::STRATEGY_AFFIRMATIVE:
                return $grant > 0;
            case self::STRATEGY_CONSENSUS:
                if ($grant > $deny) {
                    return true;
                }

                if ($deny > $grant) {
                    return false;
                }

                if ($grant === $deny && $grant !== 0) {
                    return $this->allowIfEqualGrantedDeniedDecisions;
                }

                return $this->allowIfAllAbstainDecisions;
            case self::STRATEGY_UNANIMOUS:
                if ($deny > 0) {
                    return false;
                }

                if ($grant > 0) {
                    return true;
                }

                return $this->allowIfAllAbstainDecisions;
            default:
                return false;
        }
    }
}
