<?php

namespace Innmind\ProvisionerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Innmind\ProvisionerBundle\TriggerManager;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('innmind_provisioner');

        $rootNode
            ->children()
                ->arrayNode('threshold')
                    ->children()
                        ->arrayNode('cpu')
                            ->children()
                                ->integerNode('max')
                                    ->defaultValue(100)
                                    ->min(0)
                                ->end()
                                ->integerNode('min')
                                    ->defaultValue(0)
                                    ->min(0)
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('load_average')
                            ->children()
                                ->floatNode('max')
                                    ->defaultValue(100)
                                    ->min(0)
                                ->end()
                                ->floatNode('min')
                                    ->defaultValue(0)
                                    ->min(0)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('triggers')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('trigger_manager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('strategy')
                            ->defaultValue(TriggerManager::STRATEGY_AFFIRMATIVE)
                        ->end()
                        ->booleanNode('allow_if_all_abstain')
                            ->defaultFalse()
                        ->end()
                        ->booleanNode('allow_if_equal_granted_denied')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('alerting')
                    ->children()
                        ->scalarNode('email')->end()
                        ->arrayNode('webhook')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('hipchat')
                            ->children()
                                ->scalarNode('token')->end()
                                ->scalarNode('room')->end()
                            ->end()
                        ->end()
                        ->arrayNode('slack')
                            ->children()
                                ->scalarNode('token')->end()
                                ->scalarNode('channel')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('rabbitmq')
                    ->children()
                        ->arrayNode('queue_depth')
                            ->children()
                                ->integerNode('history_length')
                                    ->min(1)
                                    ->defaultValue(10)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
