<?php

namespace Opifer\MailingListBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('opifer_mailing_list');

        $this->addBlocksSection($rootNode);
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('mailplus')
                    ->children()
                        ->scalarNode('consumer_key')->defaultValue('')->end()
                        ->scalarNode('consumer_secret')->defaultValue('')->end()
                    ->end()
                ->end()
                ->arrayNode('activecampaign')
                    ->children()
                        ->scalarNode('url')->defaultValue('')->end()
                        ->scalarNode('key')->defaultValue('')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
    /**
     * Add Block specific configuration.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addBlocksSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('blocks')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('subscribe')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferMailingListBundle:Block:subscribe.html.twig')->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([])
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
