<?php

namespace Opifer\RedirectBundle\DependencyInjection;

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
        $rootNode = $builder->root('opifer_redirect');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('redirect')
                    ->children()
                        ->scalarNode('class')
                            ->isRequired()
                        ->end()
                        ->scalarNode('manager')
                            ->defaultValue('opifer.redirect.redirect_manager.default')
                        ->end()
                        ->arrayNode('views')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('index')
                                    ->defaultValue('OpiferRedirectBundle:Redirect:index.html.twig')
                                ->end()
                                ->scalarNode('create')
                                    ->defaultValue('OpiferRedirectBundle:Redirect:create.html.twig')
                                ->end()
                                ->scalarNode('edit')
                                    ->defaultValue('OpiferRedirectBundle:Redirect:edit.html.twig')
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
