<?php

namespace Opifer\FormBundle\DependencyInjection;

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
class OpiferFormExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Prepend our config before other bundles, so we can preset
     * their config with our parameters.
     *
     * @param ContainerBuilder $container
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
            'Opifer\FormBundle\Model\FormInterface' => $config['form']['class'],
            'Opifer\FormBundle\Model\PostInterface' => $config['post']['class'],
        ];

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
            'opifer_form.from_email' => $config['from_email'],
            'opifer_form.form_class' => $config['form']['class'],
            'opifer_form.form_index_view' => $config['form']['views']['index'],
            'opifer_form.form_create_view' => $config['form']['views']['create'],
            'opifer_form.form_edit_view' => $config['form']['views']['edit'],

            'opifer_form.post_class' => $config['post']['class'],
            'opifer_form.post_index_view' => $config['post']['views']['index'],
            'opifer_form.post_view_view' => $config['post']['views']['view'],
        ];

        return $params;
    }
}
