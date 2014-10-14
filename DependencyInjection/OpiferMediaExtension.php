<?php

namespace Opifer\MediaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpiferMediaExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Prepend our mediabundle config before all other bundles, so we can preset
     * their config with our parameters
     *
     * @param  ContainerBuilder $container
     *
     * @return void
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $parameters = $this->getParameters($config);
        foreach ($parameters as $key => $value) {
            $container->setParameter($key, $value);
        }

        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'doctrine':
                    $container->prependExtensionConfig($name,  array(
                        'orm' => array(
                            'resolve_target_entities' => array(
                                'Opifer\MediaBundle\Model\MediaInterface' => $config['media_class'],
                            ),
                        ),
                    ));
                    break;
            }
        }
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

        $params['opifer_media.model.class'] = $config['media_class'];

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
