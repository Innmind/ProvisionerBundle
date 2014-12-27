<?php

namespace Innmind\ProvisionerBundle\Tests\RabbitMQ;

use Innmind\ProvisionerBundle\RabbitMQ\QueueHistory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class QueueHistoryTests extends \PHPUnit_Framework_TestCase
{
    protected $h;

    public function setUp()
    {
        $this->h = new QueueHistory();
        $this->h->setStoreDirectory(__DIR__.'/../../../../../app/cache/test/innmind_provisioner');
        $this->h->setFilesystem(new Filesystem());
        $this->h->setFinder(new Finder());
    }

    public function testSetKeyValuePair()
    {
        $this->h->put('some.key', [1,2,3]);
        $this->assertEquals([1,2,3], $this->h->get('some.key'));
    }

    public function testSetHistoryLength()
    {
        $this->h->setHistoryLength(2);
        $this->h->put('foo', [1,2,3]);
        $this->assertEquals([2,3], $this->h->get('foo'));
    }
}
