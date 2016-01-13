<?php

namespace Opifer\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     * @see http://symfony.com/doc/current/components/config/definition.html
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $rootNode = $builder->root('opifer_cms');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('allowed_locales')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                    ->defaultValue(array('nl_NL'))
                ->end()

                ->arrayNode('autocomplete')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('property')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('pagination')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('limit')->min(0)->defaultValue(2)->end()
                    ->end()
                ->end()

                ->scalarNode('google_captcha_site_key')->defaultNull()->end()
                ->scalarNode('google_captcha_secret')->defaultNull()->end()

            ->end()
        ;

        return $builder;
    }
}
