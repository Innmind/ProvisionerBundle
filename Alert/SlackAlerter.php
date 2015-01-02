<?php

namespace Innmind\ProvisionerBundle\Alert;

use Frlnc\Slack\Core\Commander;
use Psr\Log\LoggerInterface;

/**
 * Sends a channel notification when an alert is raised
 */
class SlackAlerter implements AlerterInterface
{
    protected $commander;
    protected $channel;
    protected $logger;

    /**
     * Set the slack command runner
     *
     * @param Commander $commander
     */
    public function setCommander(Commander $commander)
    {
        $this->commander = $commander;
    }

    /**
     * Set the channel where to send notifications to
     *
     * @param string $channel
     */
    public function setChannel($channel)
    {
        $this->channel = (string) $channel;
    }

    /**
     * Set the logger
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function alert(Alert $alert)
    {
        if ($alert->isOverUsed()) {
            $text = sprintf(
                'Server at full capacity! Command: %s | CPU: %s | Load: %s | Required: %s | Running: %s',
                (string) $alert->getCommandInput(),
                $alert->getCpuUsage(),
                $alert->getLoadAverage(),
                $alert->getLeftOver(),
                $alert->getRunningProcesses()
            );
        } else {
            $text = sprintf(
                'Server under used. You may take it down! Command: %s | CPU: %s | Load: %s',
                (string) $alert->getCommandInput(),
                $alert->getCpuUsage(),
                $alert->getLoadAverage()
            );
        }

        $response = $this->commander->execute('chat.postMessage', [
            'channel' => $this->channel,
            'text' => $text,
        ]);

        if ($this->logger && !$response['ok']) {
            $this->logger->error('Slack notification didn\'t worked', [
                'channel' => $this->channel,
                'response' => $response,
            ]);
        }
    }
}
