<?php

namespace Opifer\RedirectBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpiferRedirectExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setAlias('opifer.redirect.redirect_manager', $config['redirect']['manager']);
        $container->setParameter('opifer_redirect.redirect_class', $config['redirect']['class']);
        $container->setParameter('opifer_redirect.redirect_index_view', $config['redirect']['views']['index']);
        $container->setParameter('opifer_redirect.redirect_create_view', $config['redirect']['views']['create']);
        $container->setParameter('opifer_redirect.redirect_edit_view', $config['redirect']['views']['edit']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
