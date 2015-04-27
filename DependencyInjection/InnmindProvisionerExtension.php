<?php

namespace Innmind\ProvisionerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Innmind\ProvisionerBundle\Voter\StandardVoter;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class InnmindProvisionerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('innmind_provisioner', $config);

        $triggerManager = $container->getDefinition('innmind_provisioner.trigger_manager');

        $triggerManager->replaceArgument(0, $config['trigger_manager']['strategy']);
        $triggerManager->replaceArgument(1, $config['trigger_manager']['allow_if_equal_granted_denied']);
        $triggerManager->replaceArgument(2, $config['trigger_manager']['allow_if_all_abstain']);
        $triggerManager->addMethodCall('addVoter', [new StandardVoter($config['triggers'])]);

        $container
            ->getDefinition('innmind_provisioner.decision_manager')
            ->addMethodCall(
                'setCpuThreshold',
                [$config['threshold']['cpu']['max']]
            );

        $container
            ->getDefinition('innmind_provisioner.rabbitmq.queue_history')
            ->addMethodCall(
                'setHistoryLength',
                [$config['rabbitmq']['queue_depth']['history_length']]
            );

        $alert = $container
            ->getDefinition('innmind_provisioner.listener.alert')
            ->addMethodCall(
                'setCpuThresholds',
                [
                    $config['threshold']['cpu']['min'],
                    $config['threshold']['cpu']['max'],
                ]
            )
            ->addMethodCall(
                'setLoadAverageThresholds',
                [
                    $config['threshold']['load_average']['min'],
                    $config['threshold']['load_average']['max'],
                ]
            );

        if (isset($config['alerting']['email'])) {
            $alert->addMethodCall(
                'addAlerter',
                [new Reference('innmind_provisioner.alerter.email')]
            );
            $container
                ->getDefinition('innmind_provisioner.alerter.email')
                ->addMethodCall(
                    'setRecipient',
                    [$config['alerting']['email']]
                )
                ->addMethodCall(
                    'setMailer',
                    [new Reference('mailer')]
                );
        }
        if (
            isset($config['alerting']['webhook']) &&
            !empty($config['alerting']['webhook'])
        ) {
            $alert->addMethodCall(
                'addAlerter',
                [new Reference('innmind_provisioner.alerter.webhook')]
            );
            $webhook = $container->getDefinition('innmind_provisioner.alerter.webhook');

            foreach ($config['alerting']['webhook'] as $uri) {
                $webhook->addMethodCall(
                    'addUri',
                    [$uri]
                );
            }
        }

        if (
            isset($config['alerting']['hipchat']) &&
            !empty($config['alerting']['hipchat'])
        ) {
            $alert->addMethodCall(
                'addAlerter',
                [new Reference('innmind_provisioner.alerter.hipchat')]
            );

            $container
                ->getDefinition('innmind_provisioner.alerter.hipchat.oauth')
                ->replaceArgument(0, $config['alerting']['hipchat']['token']);

            $container
                ->getDefinition('innmind_provisioner.alerter.hipchat')
                ->addMethodCall(
                    'setRoom',
                    [$config['alerting']['hipchat']['room']]
                );
        }

        if (
            isset($config['alerting']['slack']) &&
            !empty($config['alerting']['slack'])
        ) {
            $alert->addMethodCall(
                'addAlerter',
                [new Reference('innmind_provisioner.alerter.slack')]
            );

            $container
                ->getDefinition('innmind_provisioner.alerter.slack.commander')
                ->replaceArgument(0, $config['alerting']['slack']['token']);

            $container
                ->getDefinition('innmind_provisioner.alerter.slack')
                ->addMethodCall(
                    'setChannel',
                    [$config['alerting']['slack']['channel']]
                );
        }
    }
}
