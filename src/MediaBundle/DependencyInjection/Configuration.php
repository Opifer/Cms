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
                ->arrayNode('media')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')
                            ->isRequired()
                        ->end()
                        ->scalarNode('manager')
                            ->defaultValue('opifer.media.media_manager.default')
                        ->end()
                        ->arrayNode('views')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('index')
                                    ->defaultValue('OpiferMediaBundle:Media:index.html.twig')
                                ->end()
                                ->scalarNode('create')
                                    ->defaultValue('OpiferMediaBundle:Media:create.html.twig')
                                ->end()
                                ->scalarNode('edit')
                                    ->defaultValue('OpiferMediaBundle:Media:edit.html.twig')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('providers')
                    ->addDefaultsIfNotSet()
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
                                ->arrayNode('proxies')
                                    ->prototype('scalar')->end()
                                ->end()
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
