<?php

namespace Innmind\ProvisionerBundle\Tests\Alert;

use Innmind\ProvisionerBundle\Alert\WebhookAlerter;
use Innmind\ProvisionerBundle\Alert\AlerterInterface;
use Innmind\ProvisionerBundle\Alert\Alert;
use Symfony\Component\Console\Input\ArrayInput;
use GuzzleHttp\Client;

class WebhookAlerterTest extends \PHPUnit_Framework_TestCase
{
    public function testCallUri()
    {
        $client = new FakeHttpClient();
        $alerter = new WebhookAlerter();
        $alerter->setHttpClient($client);

        $alerter->addUri('http://localhost/foo');
        $alerter->addUri('http://localhost/bar');

        $alert = new Alert();
        $alert
            ->setUnderUsed()
            ->setCommandName('foo')
            ->setCommandInput(new ArrayInput([]))
            ->setCpuUsage(100)
            ->setLoadAverage(4)
            ->setLeftOver(0);

        $alerter->alert($alert);

        $this->assertEquals(2, $client->getCalls());
    }
}

class FakeHttpClient extends Client
{
    protected $calls = 0;

    public function post($url = null, array $options = [])
    {
        $this->calls++;
    }

    public function getCalls()
    {
        return $this->calls;
    }
}
