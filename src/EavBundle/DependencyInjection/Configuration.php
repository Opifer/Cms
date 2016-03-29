<?php

namespace Opifer\EavBundle\DependencyInjection;

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
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('opifer_eav');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('attribute_class')
                    ->isRequired()
                ->end()
                ->scalarNode('media_class')
                    ->defaultValue('')
                ->end()
                ->scalarNode('option_class')
                    ->isRequired()
                ->end()
                ->scalarNode('schema_class')
                    ->isRequired()
                ->end()
                ->scalarNode('valueset_class')
                    ->isRequired()
                ->end()

                ->arrayNode('entities')
                    ->info('All entities should implement Opifer\EavBundle\Model\EntityInterface')
                    ->treatNullLike(array())
                    ->useAttributeAsKey('label')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
