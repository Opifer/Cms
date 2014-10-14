<?php

namespace Opifer\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpiferMediaExtension extends Extension
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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

     /**
     * Simplifying parameter syntax
     *
     * @param  array $config
     * @return array
     */
    public function getParameters(array $config)
    {
        $params = [];

        foreach ($config['providers'] as $provider => $options) {
            foreach ($options as $param => $value) {
                $params['opifer_media.providers.'.$provider.'.'.$param] = $value;
            }
        }

        $params['opifer_media.default_storage'] = $config['default_storage'];

        foreach ($config['storages'] as $storage => $options) {
            foreach ($options as $param => $value) {
                $params['opifer_media.storages.'.$storage.'.'.$param] = $value;
            }
        }
        
        return $params;
    }
}
