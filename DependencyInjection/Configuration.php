<?php

namespace Wilensky\AdyenBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /** Default payment test URL */
    const DEFAULT_PAYMENT_URL = 'https://pal-test.adyen.com/pal/servlet/Payment/v18';
    
    /** Default recurring test URL */
    const DEFAULT_RECURRING_URL = 'https://pal-test.adyen.com/pal/servlet/Recurring/v18';
    
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wilensky_adyen');

        $rootNode->children()
            ->scalarNode('merchant_account')->end()
            ->scalarNode('hmac_key')->end()
            ->scalarNode('skin_code')->end()
            ->scalarNode('username')->end()
            ->scalarNode('password')->end()
            ->arrayNode('urls')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('authorize')
                        ->defaultValue(self::DEFAULT_PAYMENT_URL.'/authorise')->cannotBeEmpty()
                    ->end()
                    ->scalarNode('authorize3d')
                        ->defaultValue(self::DEFAULT_PAYMENT_URL.'/authorise3d')->cannotBeEmpty()
                    ->end()
                    ->scalarNode('capture')
                        ->defaultValue(self::DEFAULT_PAYMENT_URL.'/capture')->cannotBeEmpty()
                    ->end()
                    ->scalarNode('refund')
                        ->defaultValue(self::DEFAULT_PAYMENT_URL.'/refund')->cannotBeEmpty()
                    ->end()
                    ->scalarNode('cancel')
                        ->defaultValue(self::DEFAULT_PAYMENT_URL.'/cancel')->cannotBeEmpty()
                    ->end()
                    ->scalarNode('cancelOrRefund')
                        ->defaultValue(self::DEFAULT_PAYMENT_URL.'/cancelOrRefund')->cannotBeEmpty()
                    ->end()
                    ->scalarNode('listRecurringDetails')
                        ->defaultValue(self::DEFAULT_RECURRING_URL.'/listRecurringDetails')->cannotBeEmpty()
                    ->end()
                    ->scalarNode('recurringTokenLookup')
                        ->defaultValue(self::DEFAULT_RECURRING_URL.'/tokenLookup')->cannotBeEmpty()
                    ->end()
                    ->scalarNode('recurringDisable')
                        ->defaultValue(self::DEFAULT_RECURRING_URL.'/disable')->cannotBeEmpty()
                    ->end()
                ->end()
            ->end() // urls
        ->end();

        return $treeBuilder;
    }
}
