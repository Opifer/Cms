<?php

namespace Opifer\ContentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
        $node = $builder->root('opifer_content');

        $node
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
                                ->scalarNode('index')->defaultValue('OpiferContentBundle:Content:index.html.twig')->end()
                                ->scalarNode('type')->defaultValue('OpiferContentBundle:Content:type.html.twig')->end()
                                ->scalarNode('new')->defaultValue('OpiferContentBundle:Content:new.html.twig')->end()
                                ->scalarNode('edit')->defaultValue('OpiferContentBundle:Content:edit.html.twig')->end()
                                ->scalarNode('design')->defaultValue('OpiferContentBundle:Content:design.html.twig')->end()
                                ->scalarNode('history')->defaultValue('OpiferContentBundle:Content:history.html.twig')->end()
                                ->scalarNode('details')->defaultValue('OpiferContentBundle:Content:details.html.twig')->end()
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
                                ->scalarNode('index')->defaultValue('OpiferContentBundle:ContentType:index.html.twig')->end()
                                ->scalarNode('create')->defaultValue('OpiferContentBundle:ContentType:create.html.twig')->end()
                                ->scalarNode('edit')->defaultValue('OpiferContentBundle:ContentType:edit.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('block')
                    ->children()
                        ->arrayNode('views')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('shared')->defaultValue('OpiferContentBundle:Block:shared.html.twig')->end()
                                ->scalarNode('shared_edit')->defaultValue('OpiferContentBundle:Block:shared_edit.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

            ->end()
        ;

        $this->addBlocksSection($node);

        return $builder;
    }

    /**
     * Add Block specific configuration
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
                        ->arrayNode('button')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/button.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->defaultValue([
                                        'btn-sm', 'btn-lg', 'btn-primary', 'btn-default', 'btn-block', 'center-block'
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('carousel')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/carousel.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('carousel_slide')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/carousel_slide.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(['default'])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('collection')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/collection.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('iframe')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/iframe.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(['default'])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('javascript')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/javascript.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('column')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Layout/layout.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('container')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Layout/layout.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('section')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Layout/section.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('gallery')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/gallery.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('html')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/html.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/image.html.twig')->end()
                                ->arrayNode('allowed_filters')
                                    ->prototype('scalar')->end()
                                    ->defaultValue([
                                        'medialibrary', 'dashboard_content'
                                    ])
                                ->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->defaultValue([
                                        'img-responsive' => 'Responsive image',
                                        'img-rounded' => 'Rounded image',
                                        'img-circle' => 'Circular image',
                                        'img-thumbnail' => 'Thumbnail image',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('jumbotron')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/jumbotron.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->defaultValue([
                                        'jumbotron-sm' => 'Jumbotron small',
                                        'jumbotron-md' => 'Jumbotron medium',
                                        'jumbotron-lg' => 'Jumbotron large',
                                        'text-regular' => 'Text-color regular',
                                        'text-contrast' => 'Text-color contrast',
                                    ])
                                ->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'default' => 'Default',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('list')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/list.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                ->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'list_simple' => 'Simple list',
                                        'tiles' => 'Tiles',
                                        'tiles_text' => 'Tiles with description'
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('navigation')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/navigation.html.twig')->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'navbar' => 'Navigation bar',
                                        'sitemap' => 'Sitemap',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('tabnav')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Layout/tabnav.html.twig')->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'tabs-horizontal' => 'Horizontal tabs',
                                        'pills-horizontal' => 'Horizontal pills',
                                        'tabs-vertical' => 'Vertical tabs',
                                        'pills-vertical' => 'Vertical pills',
                                    ])
                                ->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('css')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/css.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('pointer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/pointer.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
