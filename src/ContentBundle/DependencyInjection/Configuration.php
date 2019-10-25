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
                ->scalarNode('frontend_url')->defaultNull()->end()
                ->arrayNode('content')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')
                            ->isRequired()
                        ->end()
                        ->arrayNode('views')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('index')->defaultValue('OpiferContentBundle:Content:index.html.twig')->end()
                                ->scalarNode('edit_type')->defaultValue('OpiferContentBundle:Content:edit_type.html.twig')->end()
                                ->scalarNode('select_type')->defaultValue('OpiferContentBundle:Content:select_type.html.twig')->end()
                                ->scalarNode('select_site')->defaultValue('OpiferContentBundle:Content:select_site.html.twig')->end()
                                ->scalarNode('select_layout_type')->defaultValue('OpiferContentBundle:Content:select_layout_type.html.twig')->end()
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
                ->scalarNode('cache_provider')->defaultValue('Doctrine\Common\Cache\ArrayCache')->end()
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
                        ->arrayNode('alert')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/alert.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->defaultValue([
                                        'Primary' => 'primary',
                                        'Success' => 'success',
                                        'Info' => 'info',
                                        'Warning' => 'warning',
                                        'Danger' => 'danger',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('button')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/button.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->defaultValue([
                                        'Button small' => 'btn-sm',
                                        'Button large' => 'btn-lg',
                                        'Button primary' => 'btn-primary',
                                        'Button default' => 'btn-default',
                                        'Button block' => 'btn-block',
                                        'Center block' => 'center-block',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('card')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/card.html.twig')->end()
                                ->arrayNode('presets')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'Default' => 'default',
                                        'Card' => 'card',
                                        'Card overlay' => 'card-overlay',
                                        'List' => 'list',
                                        'News' => 'news',
                                    ])
                                ->end()
                                ->arrayNode('backgrounds')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'Default' => 'default',
                                    ])
                                ->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->defaultValue([
                                        'Image Background' => 'card-img-bg',
                                        'Inverse' => 'card-inverse',
                                        'Primary' => 'card-primary',
                                        'Success' => 'card-success',
                                        'Info' => 'card-info',
                                        'Warning' => 'card-warning',
                                        'Danger' => 'card-danger',
                                        'Primary Outline' => 'card-outline-primary',
                                        'Secondary Outline' => 'card-outline-secondary',
                                        'Success Outline' => 'card-outline-success',
                                        'Info Outline' => 'card-outline-info',
                                        'Warning Outline' => 'card-outline-warning',
                                        'Danger Outline' => 'card-outline-danger',
                                        'Card text top' => 'card-xs-top',
                                        'Card text middle' => 'card-xs-middle',
                                        'Card text bottom' => 'card-xs-bottom',
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
                                ->arrayNode('filter_placement')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'Above the collection' => 'top',
                                        'In a modal window' => 'modal',
                                    ])
                                ->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([])
                                ->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'Default' => 'default',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('content_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/content_item.html.twig')->end()
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
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Layout/column.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->defaultValue([
                                        'label.column_m_t_md' => 'm-t-md',
                                        'label.column_m_b_md' => 'm-b-md',
                                        'label.column_m_t_lg' => 'm-t-lg',
                                        'label.column_m_b_lg' => 'm-b-lg',
                                    ])
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('container')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Layout/container.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                ->end()
                                ->arrayNode('spacing_box_model')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->defaultValue([
                                        'Spacing outside (margin)' => 'm',
                                        'Spacing inside (padding)' => 'p',
                                    ])
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
                                    ->defaultValue([
                                        'Small vertical padding' => 'py-1',
                                        'Medium vertical padding' => 'py-2',
                                        'Large vertical padding' => 'py-3',
                                        'Small vertical margin' => 'my-1',
                                        'Medium vertical margin' => 'my-2',
                                        'Large vertical margin' => 'my-3',
                                    ])
                                ->end()
                                ->arrayNode('spacing_box_model')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->defaultValue([
                                        'Spacing outside (margin)' => 'm',
                                        'Spacing inside (padding)' => 'p',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('gallery')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/gallery.html.twig')->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'Default' => 'default',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('html')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/html.html.twig')->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'Default' => 'default',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/image.html.twig')->end()
                                ->arrayNode('allowed_filters')
                                    ->prototype('scalar')->end()
                                    ->defaultValue([
                                        'dashboard_content' => 'medialibrary'
                                    ])
                                ->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->defaultValue([
                                        'Responsive image' => 'img-fluid',
                                        'Rounded image' => 'img-rounded',
                                        'Circular image' => 'img-circle',
                                        'Thumbnail image' => 'img-thumbnail',
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
                                        'Jumbotron small' => 'jumbotron-sm',
                                        'Jumbotron medium' => 'jumbotron-md',
                                        'Jumbotron large' => 'jumbotron-lg',
                                        'Text-color regular' => 'text-regular',
                                        'Text-color contrast' => 'text-contrast',
                                    ])
                                ->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'Default' => 'default',
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
                                    ->defaultValue([])
                                ->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'Default' => 'default',
                                        'Card (deprecated)' => 'card',
                                        'Card overlay (deprecated)' => 'card-overlay',
                                        'List (deprecated)' => 'list',
                                        'News (deprecated)' => 'news',
                                        'Cards' => 'ListBlock--cards',
                                        'Cards overlay' => 'ListBlock--cards-overlay',
                                        'Cards overlay full width' => 'ListBlock--cards-bleed',
                                        'Cards horizontal scroll' => 'ListBlock--horizontal-scroll',
                                        'List' => 'ListBlock--list',
                                        'Media' => 'ListBlock--media',
                                        'Table' => 'ListBlock--table',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('modal')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/modal.html.twig')->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->defaultValue([])
                                ->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('navbar')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Navigation/navbar.html.twig')->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
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
                                        'Navigation bar' => 'navbar',
                                        'Sitemap' => 'sitemap',
                                    ])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('navlink')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Navigation/navlink.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('related_collection')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/related_collection.html.twig')->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([
                                        'Default' => 'default',
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
                                        'Horizontal tabs' => 'tabs-horizontal',
                                        'Horizontal pills' => 'pills-horizontal',
                                        'Vertical tabs' => 'tabs-vertical',
                                        'Vertical pills' => 'pills-vertical',
                                    ])
                                ->end()
                                ->arrayNode('styles')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('socialshare')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/socialshare.html.twig')->end()
                                ->arrayNode('templates')
                                    ->prototype('scalar')->end()
                                    ->normalizeKeys(false)
                                    ->useAttributeAsKey('name')
                                    ->defaultValue([])
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
                        ->arrayNode('video')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/video.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('cookiewall')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/cookiewall.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('downloads')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/downloads.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('breadcrumbs')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/breadcrumbs.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('avatar')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/avatar.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('search_results')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/search_results.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('data_view')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferContentBundle:Block:Content/data_view.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
