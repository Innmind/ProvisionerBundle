<?php

namespace Innmind\ProvisionerBundle\Tests\Alert;

use Innmind\ProvisionerBundle\Alert\EmailAlerter;
use Innmind\ProvisionerBundle\Alert\Alert;
use Symfony\Component\Console\Input\ArrayInput;
use Swift_Mailer;
use Swift_Transport;
use Swift_Mime_Message;
use Swift_Events_EventListener;

class EmailAlerterTest extends \PHPUnit_Framework_TestCase
{
    protected $mailer;
    protected $alerter;

    public function setUp()
    {
        $this->mailer = new FakeMailer(new FakeTransport());
        $this->alerter = new EmailAlerter();
        $this->alerter->setMailer($this->mailer);
        $this->alerter->setHost('company.tld');
    }

    protected function getAlert()
    {
        return (new Alert())
            ->setCommandName('foo')
            ->setCommandInput(new ArrayInput([]))
            ->setCpuUsage(10)
            ->setLoadAverage(1)
            ->setLeftOver(0);
    }

    public function testNoMailSent()
    {
        $this->alerter->alert($this->getAlert());

        $this->assertEquals(null, $this->mailer->getMessage());
    }

    public function testSendUnderUsed()
    {
        $alert = $this
            ->getAlert()
            ->setUnderUsed();

        $this->alerter->alert($alert);

        $this->assertEquals(
            '[Provision alert] Server under used',
            $this->mailer->getMessage()->getSubject()
        );
    }

    public function testSendOverUsed()
    {
        $alert = $this
            ->getAlert()
            ->setOverUsed();

        $this->alerter->alert($alert);

        $this->assertEquals(
            '[Provision alert] Server over used',
            $this->mailer->getMessage()->getSubject()
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
