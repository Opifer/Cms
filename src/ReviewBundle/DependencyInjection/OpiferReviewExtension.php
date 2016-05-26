<?php

namespace Opifer\ReviewBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class OpiferReviewExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $parameters = $this->getParameters($config);
        foreach ($parameters as $key => $value) {
            $container->setParameter($key, $value);
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
            'opifer_review.review_class' => $config['review']['class'],
            'opifer_review.review_index_view' => $config['review']['views']['index'],
            'opifer_review.review_create_view' => $config['review']['views']['create'],
            'opifer_review.review_edit_view' => $config['review']['views']['edit'],
        ];

        return $params;
    }
}
