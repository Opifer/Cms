<?php

namespace Opifer\ContentBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpiferContentExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('blocks.yml');
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

        $container->setAlias('opifer.content.content_manager', $config['content_manager']);
        $container->setDefinition('opifer.content.cache_provider', new Definition($config['cache_provider']));

        $parameters = $this->getParameters($config);
        foreach ($parameters as $key => $value) {
            $container->setParameter($key, $value);
        }

        $this->prependExtensionConfig($container, $config);
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
            'opifer_content.content_class' => $config['content']['class'],
            'opifer_content.content_index_view' => $config['content']['views']['index'],
            'opifer_content.content_type_view' => $config['content']['views']['type'],
            'opifer_content.content_edit_type' => $config['content']['views']['edit_type'],
            'opifer_content.content_select_type' => $config['content']['views']['select_type'],
            'opifer_content.content_select_site' => $config['content']['views']['select_site'],
            'opifer_content.content_select_layout_type' => $config['content']['views']['select_layout_type'],
            'opifer_content.content_new_view' => $config['content']['views']['new'],
            'opifer_content.content_design_view' => $config['content']['views']['design'],
            'opifer_content.content_edit_view' => $config['content']['views']['edit'],
            'opifer_content.content_details_view' => $config['content']['views']['details'],
            'opifer_content.content_history_view' => $config['content']['views']['history'],
            'opifer_content.content_type_class' => $config['content_type']['class'],
            'opifer_content.content_type_index_view' => $config['content_type']['views']['index'],
            'opifer_content.content_type_create_view' => $config['content_type']['views']['create'],
            'opifer_content.content_type_edit_view' => $config['content_type']['views']['edit'],
        ];

        // Block configuration
        foreach ($config['blocks'] as $block => $blockConfig) {
            $params['opifer_content.'.$block.'_block_configuration'] = $blockConfig;
        }

        return $params;
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function prependExtensionConfig(ContainerBuilder $container, array $config)
    {
        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'doctrine':
                    $container->prependExtensionConfig($name, [
                        'orm' => [
                            'resolve_target_entities' => [
                                'Opifer\ContentBundle\Model\ContentInterface' => $config['content']['class'],
                                'Opifer\ContentBundle\Model\ContentTypeInterface' => $config['content_type']['class'],
                            ],
                        ],
                    ]);
                    break;
            }
        }
    }
}
