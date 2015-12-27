<?php

namespace Opifer\CmsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpiferCmsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $parameters = $this->getParameters($config);
        foreach ($parameters as $key => $value) {
            $container->setParameter($key, $value);
        }

        $this->mapClassParameters($config['classes'], $container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Simplifying parameter syntax
     *
     * @param  array $config
     * @return array
     */
    public function getParameters($config)
    {
        return [
            'opifer_cms.locale' => $config['locale'],
            'opifer_cms.google_captcha_site_key' => $config['google_captcha_site_key'],
            'opifer_cms.google_captcha_secret' => $config['google_captcha_secret'],
            'opifer_cms.database_driver' => $config['database']['driver'],
            'opifer_cms.database_host' => $config['database']['host'],
            'opifer_cms.database_name' => $config['database']['name'],
            'opifer_cms.database_user' => $config['database']['user'],
            'opifer_cms.database_password' => $config['database']['password'],
            'opifer_cms.database_table_prefix' => $config['database']['table_prefix'],
            'opifer_cms.autocomplete' => $config['autocomplete'],

            // Deprecated
            'opifer_cms.allowed_locales' => ['en'],
        ];
    }

    /**
     * Remap class parameters.
     *
     * @param array $classes
     * @param ContainerBuilder $container
     */
    protected function mapClassParameters(array $classes, ContainerBuilder $container)
    {
        foreach ($classes as $model => $serviceClasses) {
            foreach ($serviceClasses as $service => $class) {
                $container->setParameter(
                    sprintf(
                        'opifer_cms.%s_%s',
                        $service,
                        $model
                    ),
                    $class
                );
            }
        }
    }
}
