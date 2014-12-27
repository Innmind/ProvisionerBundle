<?php

namespace Innmind\ProvisionerBundle\Alert;

use Innmind\ProvisionerBundle\CommandHelper;
use Swift_Mailer;
use Swift_Message;

/**
 * Send a mail when a provision alert is raised
 */
class EmailAlerter implements AlerterInterface
{
    protected $mailer;
    protected $host;
    protected $recipient;

    /**
     * Set the swift mailer
     *
     * @param Swift_Mailer $mailer
     */
    public function setMailer(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Set host name used to build 'From' email
     *
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = (string) $host;
    }

    /**
     * Set the emails recipient
     *
     * @param string $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = (string) $recipient;
    }

    /**
     * {@inheritdoc}
     */
    public function alert($type, $name, array $args, $cpuUsage, $loadAverage, $leftOver = 0)
    {
        switch ($type) {
            case self::UNDER_USED:
                $message = Swift_Message::newInstance()
                    ->setSubject('[Provision alert] Server under used')
                    ->setFrom(sprintf(
                        'provisioner@%s',
                        $this->host
                    ))
                    ->setTo($this->recipient)
                    ->setBody(
                        'Command: '.$name."\n".
                        'Command args: '.CommandHelper::getArgumentsAsString($args)."\n".
                        'CPU usage: '.$cpuUsage."\n".
                        'Load average: '.$loadAverage."\n"
                    );
                break;
            case self::OVER_USED:
                $message = Swift_Message::newInstance()
                    ->setSubject('[Provision alert] Server over used')
                    ->setFrom('provision@context.com')
                    ->setTo($this->recipient)
                    ->setBody(
                        'Command: '.$name."\n".
                        'Command args: '.CommandHelper::getArgumentsAsString($args)."\n".
                        'CPU usage: '.$cpuUsage."\n".
                        'Load average: '.$loadAverage."\n".
                        'Processes required: '.$leftOver."\n"
                    );
                break;
        }

        if (isset($message)) {
            $this->mailer->send($message);
        }
    }
}
