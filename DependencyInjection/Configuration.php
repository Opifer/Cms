<?php

namespace Opifer\MediaBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('opifer_media');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('media_class')
                    ->isRequired()
                ->end()
                ->arrayNode('providers')
                    ->children()
                        ->arrayNode('youtube')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('api_key')->defaultValue('null')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('default_storage')
                    ->defaultValue('local_storage')
                ->end()
                ->arrayNode('storages')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('local')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('directory')
                                    ->defaultValue('%kernel.root_dir%/../web/uploads')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('temp')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('directory')
                                    ->defaultValue('/tmp')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('aws_s3')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('key')->defaultValue('null')->end()
                                ->scalarNode('secret')->defaultValue('null')->end()
                                ->scalarNode('region')->defaultValue('eu-west-1')->end()
                                ->scalarNode('bucket')->defaultValue('null')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * opifer_media:
     *     youtube:
     *         google_api_key: dfgdghfgh
     */
}
