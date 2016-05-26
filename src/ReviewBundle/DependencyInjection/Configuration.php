<?php

namespace Opifer\ReviewBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
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
        $rootNode = $treeBuilder->root('opifer_review');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('review')
                    ->children()
                        ->scalarNode('class')
                            ->isRequired()
                        ->end()
                        ->arrayNode('views')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('index')
                                    ->defaultValue('OpiferReviewBundle:Review:index.html.twig')
                                ->end()
                                ->scalarNode('create')
                                    ->defaultValue('OpiferReviewBundle:Review:create.html.twig')
                                ->end()
                                ->scalarNode('edit')
                                    ->defaultValue('OpiferReviewBundle:Review:edit.html.twig')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
