<?php

namespace Innmind\ProvisionerBundle\Tests\Alert;

use Innmind\ProvisionerBundle\Alert\EmailAlerter;
use Innmind\ProvisionerBundle\Alert\AlerterInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Swift_Mailer;
use Swift_Transport;
use Swift_Mime_Message;
use Swift_Events_EventListener;

class EmailAlerterTest extends \PHPUnit_Framework_TestCase
{
    public function testNoMailSent()
    {
        $mailer = new FakeMailer(new FakeTransport());
        $alerter = new EmailAlerter();
        $alerter->setMailer($mailer);
        $alerter->setHost('company.tld');

        $alerter->alert('foo', 'foo', new ArrayInput([]), 10, 1, 0);

        $this->assertEquals(null, $mailer->getMessage());
    }

    public function testSendUnderUsed()
    {
        $mailer = new FakeMailer(new FakeTransport());
        $alerter = new EmailAlerter();
        $alerter->setMailer($mailer);
        $alerter->setHost('company.tld');

        $alerter->alert(AlerterInterface::UNDER_USED, 'foo', new ArrayInput([]), 10, 1, 0);

        $this->assertEquals(
            '[Provision alert] Server under used',
            $mailer->getMessage()->getSubject()
        );
    }

    public function testSendOverUsed()
    {
        $mailer = new FakeMailer(new FakeTransport());
        $alerter = new EmailAlerter();
        $alerter->setMailer($mailer);
        $alerter->setHost('company.tld');

        $alerter->alert(AlerterInterface::OVER_USED, 'foo', new ArrayInput([]), 10, 1, 0);

        $this->assertEquals(
            '[Provision alert] Server over used',
            $mailer->getMessage()->getSubject()
        );
    }
}

class FakeMailer extends Swift_Mailer
{
    protected $message;

    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->message = $message;

        return 1;
    }

    public function getMessage()
    {
        return $this->message;
    }
}

class FakeTransport implements Swift_Transport
{
    public function isStarted(){}
    public function start(){}
    public function stop(){}
    public function send(Swift_Mime_Message $message, &$failedRecipients = null){}
    public function registerPlugin(Swift_Events_EventListener $plugin){}
}
