<?php

namespace Opifer\FormBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('opifer_form');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('from_email')
                    ->isRequired()
                ->end()
                ->arrayNode('form')
                    ->children()
                        ->scalarNode('class')
                            ->isRequired()
                        ->end()
                        ->arrayNode('views')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('index')
                                    ->defaultValue('OpiferFormBundle:Form:index.html.twig')
                                ->end()
                                ->scalarNode('create')
                                    ->defaultValue('OpiferFormBundle:Form:create.html.twig')
                                ->end()
                                ->scalarNode('edit')
                                    ->defaultValue('OpiferFormBundle:Form:edit.html.twig')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('post')
                    ->children()
                        ->scalarNode('class')
                            ->isRequired()
                        ->end()
                        ->arrayNode('views')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('index')
                                    ->defaultValue('OpiferFormBundle:Post:index.html.twig')
                                ->end()
                                ->scalarNode('view')
                                    ->defaultValue('OpiferFormBundle:Post:view.html.twig')
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
