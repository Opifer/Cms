<?php

namespace Opifer\ContentBundle\DependencyInjection;

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
        $rootNode = $builder->root('opifer_content');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('content_class')
                    ->isRequired()
                ->end()
                ->scalarNode('directory_class')
                    ->isRequired()
                ->end()
                ->scalarNode('layout_class')
                    ->isRequired()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
