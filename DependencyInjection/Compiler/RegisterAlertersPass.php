<?php

namespace Innmind\ProvisionerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Register services tagged as services as object that will
 * be notified when an alert is raised
 */
class RegisterAlertersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $listener = $container->getDefinition('innmind_provisioner.listener.alert');
        $alerters = $container->findTaggedServiceIds(
            'innmind_provisioner.alerter'
        );

        foreach ($alerters as $id => $attributes) {
            $listener->addMethodCall(
                'addAlerter',
                [new Reference($id)]
            );
        }
    }
}
