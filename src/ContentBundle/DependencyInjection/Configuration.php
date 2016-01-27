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
                ->scalarNode('content_manager')
                    ->defaultValue('opifer.content.content_manager.default')
                ->end()
                ->arrayNode('content')
                    ->children()
                        ->scalarNode('class')
                            ->isRequired()
                        ->end()
                        ->arrayNode('views')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('index')
                                    ->defaultValue('OpiferContentBundle:Content:index.html.twig')
                                ->end()
                                ->scalarNode('type')
                                    ->defaultValue('OpiferContentBundle:Content:type.html.twig')
                                ->end()
                                ->scalarNode('new')
                                    ->defaultValue('OpiferContentBundle:Content:new.html.twig')
                                ->end()
                                ->scalarNode('edit')
                                    ->defaultValue('OpiferContentBundle:Content:edit.html.twig')
                                ->end()
                                ->scalarNode('history')
                                ->defaultValue('OpiferContentBundle:Content:history.html.twig')
                                ->end()
                                ->scalarNode('details')
                                    ->defaultValue('OpiferContentBundle:Content:details.html.twig')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('content_type')
                    ->children()
                        ->scalarNode('class')->isRequired()->end()
                        ->arrayNode('views')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('index')
                                    ->defaultValue('OpiferContentBundle:ContentType:index.html.twig')
                                ->end()
                                ->scalarNode('create')
                                    ->defaultValue('OpiferContentBundle:ContentType:create.html.twig')
                                ->end()
                                ->scalarNode('edit')
                                    ->defaultValue('OpiferContentBundle:ContentType:edit.html.twig')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
