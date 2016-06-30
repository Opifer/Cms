<?php

namespace Opifer\CmsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpiferCmsExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $parameters = $this->getParameters($config);
        foreach ($parameters as $key => $value) {
            $container->setParameter($key, $value);
        }

        $this->mapClassParameters($config['classes'], $container);
    }

    /**
     * Simplifying parameter syntax.
     *
     * @param array $config
     *
     * @return array
     */
    public function getParameters(array $config)
    {
        $params = [
            'opifer_cms.default_locale' => $config['default_locale'],
            'opifer_cms.google_captcha_site_key' => $config['google_captcha_site_key'],
            'opifer_cms.google_captcha_secret' => $config['google_captcha_secret'],
            'opifer_cms.database_driver' => $config['database']['driver'],
            'opifer_cms.database_host' => $config['database']['host'],
            'opifer_cms.database_name' => $config['database']['name'],
            'opifer_cms.database_user' => $config['database']['user'],
            'opifer_cms.database_password' => $config['database']['password'],
            'opifer_cms.database_table_prefix' => $config['database']['table_prefix'],
            'opifer_cms.autocomplete' => $config['autocomplete'],
            'opifer_cms.ckeditor_css_path' => $config['ckeditor']['css_path'],
            'opifer_cms.classes' => $config['classes'],

            // Deprecated
            'opifer_cms.allowed_locales' => ['en'],
        ];

        // Block configuration
        foreach ($config['blocks'] as $block => $blockConfig) {
            $params['opifer_cms.'.$block.'_block_configuration'] = $blockConfig;
        }

        return $params;
    }

    /**
     * Remap class parameters.
     *
     * @param array            $classes
     * @param ContainerBuilder $container
     */
    protected function mapClassParameters(array $classes, ContainerBuilder $container)
    {
        foreach ($classes as $model => $serviceClasses) {
            foreach ($serviceClasses as $service => $class) {
                $container->setParameter(
                    sprintf(
                        'opifer_cms.%s_%s',
                        $model,
                        $service
                    ),
                    $class
                );
            }
        }
    }
}
