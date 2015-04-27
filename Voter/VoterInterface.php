<?php

namespace Innmind\ProvisionerBundle\Voter;

use Symfony\Component\Console\Input\InputInterface;

interface VoterInterface
{
    const TRIGGER_GRANTED = 'granted';
    const TRIGGER_ABSTAIN = 'abstain';
    const TRIGGER_DENIED = 'denied';

    /**
     * Determine if the voter can decide for the given command name
     *
     * @param string $command
     *
     * @return bool
     */
    public function supportsCommand($command);

    /**
     * Decide if the provisioning should be triggered for the given input
     *
     * @param string $command
     * @param InputInterface $input
     *
     * @return string Either 'granted', 'abstain' or 'denied'
     */
    public function vote($command, InputInterface $input);
}
