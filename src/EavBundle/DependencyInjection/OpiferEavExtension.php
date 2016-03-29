<?php

namespace Opifer\EavBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpiferEavExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('providers.yml');
    }

    /**
     * Simplifying parameter syntax
     *
     * @param array $config
     *
     * @return array
     */
    public function getParameters(array $config)
    {
        $params = [
            'opifer_eav.attribute_class' => $config['attribute_class'],
            'opifer_eav.media_class'     => $config['media_class'],
            'opifer_eav.option_class'    => $config['option_class'],
            'opifer_eav.schema_class'  => $config['schema_class'],
            'opifer_eav.valueset_class'  => $config['valueset_class'],
        ];

        foreach ($config['entities'] as $label => $entity) {
            $params['opifer_eav.entities'][$label] = $entity;
        }

        return $params;
    }

    /**
     * Prepend our config before other bundles, so we can preset
     * their config with our parameters
     *
     * @param ContainerBuilder $container
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

        $resolvableEntities = [
            'Opifer\EavBundle\Model\AttributeInterface' => $config['attribute_class'],
            'Opifer\EavBundle\Model\OptionInterface'    => $config['option_class'],
            'Opifer\EavBundle\Model\SchemaInterface'  => $config['schema_class'],
            'Opifer\EavBundle\Model\ValueSetInterface'  => $config['valueset_class'],
        ];

        if ($config['media_class'] != '') {
            $resolvableEntities['Opifer\EavBundle\Model\MediaInterface'] = $config['media_class'];
        }

        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'doctrine':
                    $container->prependExtensionConfig($name,  [
                        'orm' => [
                            'resolve_target_entities' => $resolvableEntities,
                        ],
                    ]);
                    break;
            }
        }
    }
}
