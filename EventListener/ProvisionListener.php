<?php

namespace Innmind\ProvisionerBundle\EventListener;

use Innmind\ProvisionerBundle\Event\ProvisionEvent;
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
        $input = $event->getCommandInput();
        $toRun = $event->getCount();

        if ($toRun === 0) {
            return;
        }

        $command = sprintf(
            'cd %s && ./console %s',
            $this->appDir,
            (string) $input
        );

        for ($i = 0; $i < $toRun; $i++) {
            $process = new Process($command);
            $process->start();
        }
    }
}
