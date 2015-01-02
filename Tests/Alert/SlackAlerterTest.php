<?php

namespace Innmind\ProvisionerBundle\Tests\Alert;

use Innmind\ProvisionerBundle\Alert\SlackAlerter;
use Innmind\ProvisionerBundle\Alert\Alert;
use Symfony\Component\Console\Input\ArrayInput;
use Frlnc\Slack\Core\Commander;
use Frlnc\Slack\Http\SlackResponse;

class SlackAlerterTest extends \PHPUnit_Framework_TestCase
{
    protected $alerter;
    protected $commander;

    public function setUp()
    {
        $this->alerter = new SlackAlerter();
        $interactor = $this
            ->getMockBuilder('Frlnc\\Slack\\Http\\CurlInteractor')
            ->getMock();
        $this->commander = new FakeCommander('foo', $interactor);
        $this->alerter->setCommander($this->commander);
        $this->alerter->setChannel('#main');
    }

    public function testSendOverUsedNotification()
    {
        $alert = new Alert();
        $alert
            ->setOverUsed()
            ->setCommandName('foo')
            ->setCommandInput(new ArrayInput([]))
            ->setCpuUsage(100)
            ->setLoadAverage(24)
            ->setLeftOver(12)
            ->setRunningProcesses(12);

        $this->alerter->alert($alert);

        $this->assertEquals(
            [
                'channel' => '#main',
                'text' => 'Server at full capacity! Command:  | CPU: 100 | Load: 24 | Required: 12 | Running: 12'
            ],
            $this->commander->data
        );
    }

    public function testSendUnderUsedNotification()
    {
        $alert = new Alert();
        $alert
            ->setUnderUsed()
            ->setCommandName('foo')
            ->setCommandInput(new ArrayInput([]))
            ->setCpuUsage(10)
            ->setLoadAverage(0.1)
            ->setLeftOver(12)
            ->setRunningProcesses(12);

        $this->alerter->alert($alert);

        $this->assertEquals(
            [
                'channel' => '#main',
                'text' => 'Server under used. You may take it down! Command:  | CPU: 10 | Load: 0.1'
            ],
            $this->commander->data
        );
    }
}

class FakeCommander extends Commander
{
    public $data;

    public function execute($command, array $parameters = [])
    {
        $this->data = $parameters;

        return new SlackResponse('{}', [], 201);
    }
}
