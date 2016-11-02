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
                        ->arrayNode('choice_field')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/choice_field.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('email_field')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/email_field.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_nav_button')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/form_nav_button.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_progress')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/form_progress.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_result')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/form_result.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_section')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/form_section.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('number_field')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/number_field.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('range_field')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/range_field.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('rich_check_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/rich_check_item.html.twig')->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->defaultValue([
                                        'horizontal' => 'Horizontal',
                                        'vertical' => 'Vertical',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('text_field')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/text_field.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('date_field')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferFormBlockBundle:Block:Content/date_field.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
