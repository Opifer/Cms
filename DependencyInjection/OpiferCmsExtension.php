<?php

namespace Opifer\CmsBundle\DependencyInjection;

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
class OpiferCmsExtension extends Extension implements PrependExtensionInterface
{
    const DEFAULT_KEY = 'default';

    protected $applicationName = 'opifer_cms';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @inheritdoc
     */
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
     * Simplifying parameter syntax
     *
     * @param  array $config
     * @return array
     */
    private function getParameters($config)
    {
        return [
            'opifer_cms.pagination.limit' => $config['pagination']['limit'],
            'opifer_cms.allowed_locales' => $config['allowed_locales'],
            'opifer_cms.classes' => $config['classes']
        ];
    }

    /**
     * Remap class parameters.
     *
     * @param array $classes
     * @param ContainerBuilder $container
     */
    private function mapClassParameters(array $classes, ContainerBuilder $container)
    {
        foreach ($classes as $model => $serviceClasses) {
            foreach ($serviceClasses as $service => $class) {
                if ('form' === $service) {
                    if (!is_array($class)) {
                        $class = array(self::DEFAULT_KEY => $class);
                    }
                    foreach ($class as $suffix => $subClass) {
                        $container->setParameter(
                            sprintf(
                                '%s.form.type.%s%s.class',
                                $this->applicationName,
                                $model,
                                $suffix === self::DEFAULT_KEY ? '' : sprintf('_%s', $suffix)
                            ),
                            $subClass
                        );
                    }
                } elseif ('translation' === $service) {
                    $this->mapClassParameters(array(sprintf('%s_translation', $model) => $class), $container);
                } else {
                    $container->setParameter(
                        sprintf(
                            '%s.%s.%s.class',
                            $this->applicationName,
                            $service,
                            $model
                        ),
                        $class
                    );
                }
            }
        }
    }
}
