<?php

namespace Opifer\CmsBundle\DependencyInjection;

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
     *
     * @see http://symfony.com/doc/current/components/config/definition.html
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $rootNode = $builder->root('opifer_cms');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('default_locale')->defaultValue('en')->end()

                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Opifer\CmsBundle\Entity\User')->end()
                                ->scalarNode('repository')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('database')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')->defaultValue('pdo_mysql')->end()
                        ->scalarNode('host')->defaultValue('%database_host%')->end()
                        ->scalarNode('name')->defaultValue('%database_name%')->end()
                        ->scalarNode('user')->defaultValue('%database_user%')->end()
                        ->scalarNode('password')->defaultValue('%database_password%')->end()
                        ->scalarNode('table_prefix')->defaultValue('opifer_')->end()
                    ->end()
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

                ->arrayNode('ckeditor')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('css_path')->defaultValue('/bundles/opifercms/css/app.css')->end()
                    ->end()
                ->end()

                ->scalarNode('google_captcha_site_key')->defaultNull()->end()
                ->scalarNode('google_captcha_secret')->defaultNull()->end()

            ->end()
        ;

        return $builder;
    }
}
