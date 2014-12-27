<?php

namespace Innmind\ProvisionerBundle;

use Innmind\ProvisionerBundle\DependencyInjection\Compiler\LoadRabbitMQConfigPass;
use Innmind\ProvisionerBundle\DependencyInjection\Compiler\RegisterAlertersPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

class InnmindProvisionerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container
            ->addCompilerPass(
                new LoadRabbitMQConfigPass(),
                PassConfig::TYPE_BEFORE_REMOVING
            )
            ->addCompilerPass(
                new RegisterAlertersPass()
            );
    }
}
