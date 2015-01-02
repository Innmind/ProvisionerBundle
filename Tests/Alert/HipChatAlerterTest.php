<?php

namespace Innmind\ProvisionerBundle\Tests\Alert;

use Innmind\ProvisionerBundle\Alert\HipChatAlerter;
use Innmind\ProvisionerBundle\Alert\Alert;
use GorkaLaucirica\HipchatAPIv2Client\Client;
use GorkaLaucirica\HipchatAPIv2Client\API\RoomAPI;
use Symfony\Component\Console\Input\ArrayInput;

class HipChatAlerterTest extends \PHPUnit_Framework_TestCase
{
    protected $alerter;
    protected $client;

    public function setUp()
    {
        $this->alerter = new HipChatAlerter();
        $auth = $this
            ->getMockBuilder('GorkaLaucirica\\HipchatAPIv2Client\\Auth\\AuthInterface')
            ->getMock();
        $this->client = new FakeClient($auth);
        $api = new RoomAPI($this->client);
        $this->alerter->setRoomApi($api);
        $this->alerter->setRoom('main');
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
                'id' => null,
                'from' => null,
                'color' => 'red',
                'message' => 'Server at full capacity! Command:  | CPU: 100 | Load: 24 | Required: 12 | Running: 12',
                'notify' => true,
                'message_format' => 'text',
                'date' => null
            ],
            $this->client->content
        );
        $this->assertEquals(
            '/v2/room/main/notification',
            $this->client->resource
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
                'id' => null,
                'from' => null,
                'color' => 'yellow',
                'message' => 'Server under used. You may take it down! Command:  | CPU: 10 | Load: 0.1',
                'notify' => true,
                'message_format' => 'text',
                'date' => null
            ],
            $this->client->content
        );
        $this->assertEquals(
            '/v2/room/main/notification',
            $this->client->resource
        );
    }
}

class FakeClient extends Client
{
    public $content;
    public $resource;

    public function post($resource, $content)
    {
        $this->resource = $resource;
        $this->content = $content;

        return ['ok' => true];
    }
}
