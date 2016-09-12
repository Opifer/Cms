<?php

namespace Opifer\FormBlockBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $rootNode = $builder->root('opifer_formblock');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('blocks')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/form.html.twig')->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'default' => 'Default'
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_field')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/formfield.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_result')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/formresult.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_section')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/formsection.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
