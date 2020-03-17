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
                        ->arrayNode('config')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Opifer\CmsBundle\Entity\Config')->end()
                            ->end()
                        ->end()
                        ->arrayNode('review')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Opifer\CmsBundle\Entity\Review')->end()
                            ->end()
                        ->end()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Opifer\CmsBundle\Entity\User')->end()
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

                ->arrayNode('default_content_access')
                    ->scalarPrototype()->end()
                    ->defaultValue(['ROLE_SUPER_ADMIN','ROLE_ADMIN'])
                ->end()

                ->arrayNode('permissions')
                    ->useAttributeAsKey('role')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                    ->defaultValue([
                        'ROLE_USER' => [],
                        'ROLE_ADMIN' => [],
                        'ROLE_SUPER_ADMIN' => [
                            'CONFIG_INDEX',

                            'CONTENT_INDEX',
                            'CONTENT_CREATE',
                            'CONTENT_DELETE',
                            'CONTENT_DUPLICATE',

                            'CONTENT_TYPE_INDEX',
                            'CONTENT_TYPE_CREATE',
                            'CONTENT_TYPE_DELETE',
                            'CONTENT_TYPE_EDIT',

                            'CRONJOB_INDEX',
                            'CRONJOB_CREATE',
                            'CRONJOB_EDIT',
                            'CRONJOB_DELETE',
                            'CRONJOB_RESET',

                            'DASHBOARD_INDEX',

                            'DOMAIN_INDEX',
                            'DOMAIN_CREATE',
                            'DOMAIN_EDIT',

                            'FORM_INDEX',
                            'FORM_CREATE',
                            'FORM_DELETE',
                            'FORM_EDIT',

                            'LAYOUT_INDEX',
                            'LAYOUT_CREATE',
                            'LAYOUT_EDIT',
                            'LAYOUT_DETAILS',
                            'LAYOUT_DELETE',

                            'LOCALE_INDEX',
                            'LOCALE_CREATE',
                            'LOCALE_EDIT',

                            'MAILINGLIST_INDEX',
                            'MAILINGLIST_CREATE',
                            'MAILINGLIST_EDIT',
                            'MAILINGLIST_SUBSCRIPTIONS',

                            'MAILINGLIST_DELETE',

                            'MEDIA_INDEX',
                            'MEDIA_CREATE',
                            'MEDIA_EDIT',
                            'MEDIA_DELETE',

                            'POST_INDEX',
                            'POST_DELETE',
                            'POST_NOTIFICATION',
                            'POST_LIST',
                            'POST_VIEW',

                            'REDIRECT_INDEX',
                            'REDIRECT_CREATE',
                            'REDIRECT_DELETE',
                            'REDIRECT_EDIT',

                            'REVIEW_INDEX',
                            'REVIEW_CREATE',
                            'REVIEW_EDIT',
                            'REVIEW_DELETE',

                            'SITE_INDEX',
                            'SITE_CREATE',
                            'SITE_EDIT',
                            'SITE_DELETE',

                            'SUBSCRIPTION_INDEX',

                            'TEMPLATE_INDEX',
                            'TEMPLATE_CREATE',
                            'TEMPLATE_EDIT',
                            'TEMPLATE_DELETE',

                            'USER_INDEX',
                            'USER_EDIT',
                            'USER_PROFILE',
                            'USER_CREATE',
                        ]
                    ])
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

                ->arrayNode('blocks')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('login')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('view')->defaultValue('OpiferCmsBundle:Block:Content/login.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('google_tag_manager_key')->defaultNull()->end()
                ->scalarNode('google_captcha_site_key')->defaultNull()->end()
                ->scalarNode('google_captcha_secret')->defaultNull()->end()

            ->end()
        ;

        return $builder;
    }
}
