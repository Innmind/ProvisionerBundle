<?php

namespace Innmind\ProvisionerBundle\Tests\Listener;

use Innmind\ProvisionerBundle\Listener\ConsoleListener;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ConsoleListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testReduce()
    {
        $listener = new ConsoleListener();
        $definition = new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED),
            new InputArgument('name', InputArgument::REQUIRED),
            new InputArgument('whatever', InputArgument::OPTIONAL),
            new InputOption('messages', 'm', InputOption::VALUE_REQUIRED),
            new InputOption('env', 'e', InputOption::VALUE_REQUIRED, '', 'dev'),
            new InputOption('memory-limit', 'l', InputOption::VALUE_OPTIONAL),
            new InputOption('route', 'r', InputOption::VALUE_OPTIONAL),
            new InputOption('debug', 'd', InputOption::VALUE_OPTIONAL),
            new InputOption('config', 'c', InputOption::VALUE_OPTIONAL),
        ]);
        $argv = new ArgvInput([
            'console',
            'rabbitmq:consumer',
            'resource',
            'irrelevant',
            '-m 50',
            '--env=prod',
        ], $definition);

        $this->assertEquals(
            [
                'resource',
                'irrelevant',
                'messages' => '50',
                'env' => 'prod',
            ],
            $listener->reduce($argv)
        );
    }
}
