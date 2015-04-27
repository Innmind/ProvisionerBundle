<?php

namespace Innmind\ProvisionerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Register services tagged as objects used to determine if the provisioner
 * should be started
 */
class RegisterVotersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $listener = $container->getDefinition('innmind_provisioner.trigger_manager');
        $alerters = $container->findTaggedServiceIds(
            'innmind_provisioner.voter'
        );

        foreach ($alerters as $id => $attributes) {
            $listener->addMethodCall(
                'addVoter',
                [new Reference($id)]
            );
        }
    }
}
