<?php

namespace Innmind\ProvisionerBundle\Listener;

use Innmind\ProvisionerBundle\Event\ProvisionEvent;
use Innmind\ProvisionerBundle\CommandHelper;
use Symfony\Component\Process\Process;

/**
 * Listen when new commands must be run
 */
class ProvisionListener
{
    protected $appDir;

    /**
     * Set the application root directory
     *
     * @param string $directory
     */
    public function setAppDirectory($directory)
    {
        $this->appDir = (string) $directory;
    }

    /**
     * Run the specified number of symfony commands in the background
     *
     * @param ProvisionEvent $event
     */
    public function handle(ProvisionEvent $event)
    {
        $name = $event->getCommandName();
        $args = $event->getCommandArguments();
        $toRun = $event->getCount();

        if ($toRun === 0) {
            return;
        }

        $command = sprintf(
            'cd %s && ./console %s %s',
            $this->appDir,
            $name,
            CommandHelper::getArgumentsAsString($args)
        );

        for ($i = 0; $i < $toRun; $i++) {
            $process = new Process($command);
            $process->start();
            var_dump($process->isStarted());
        }
    }
}
