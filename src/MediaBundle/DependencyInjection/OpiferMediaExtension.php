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

        $container->setAlias('opifer.media.media_manager', $config['media']['manager']);

        $parameters = $this->getParameters($config);
        foreach ($parameters as $key => $value) {
            $container->setParameter($key, $value);
        }

        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'doctrine':
                    $container->prependExtensionConfig($name,  [
                        'orm' => [
                            'resolve_target_entities' => [
                                'Opifer\MediaBundle\Model\MediaInterface' => $config['media']['class'],
                            ],
                        ],
                    ]);
                    break;
                case 'twig':
                    $container->prependExtensionConfig($name, [
                        'form' => [
                            'resources' => [
                                'OpiferMediaBundle:Form:fields.html.twig'
                            ]
                        ]
                    ]);
                    break;
                case 'liip_imagine':
                    $container->prependExtensionConfig($name, [
                        'resolvers' => [
                            'local_storage' => [
                                'web_path' => null
                            ],
                            'aws_storage' => [
                                'aws_s3' => [
                                    'client_config' => [
                                        'key'    => $config['storages']['aws_s3']['key'],
                                        'secret' => $config['storages']['aws_s3']['secret'],
                                        'region' => $config['storages']['aws_s3']['region']
                                    ],
                                    'bucket' => $config['storages']['aws_s3']['bucket']
                                ]
                            ]
                        ],
                        'cache' => $config['default_storage'],
                        'data_loader' => 'stream.file_storage',
                        'loaders' => [
                            'stream.file_storage' => [
                                'stream' => [
                                    'wrapper' => 'gaufrette://file_storage/'
                                ]
                            ]
                        ],
                        'driver' => 'imagick',
                        'filter_sets' => [
                            'medialibrary' => [
                                'quality' => 100,
                                'filters' => [
                                    'relative_resize' => ['heighten' => 160]
                                ]
                            ]
                        ]
                    ]);
                    break;
                case 'knp_gaufrette':
                    $container->prependExtensionConfig($name, [
                        'adapters' => [
                            'tmp_storage' => [
                                'local' => [
                                    'directory' => $config['storages']['temp']['directory']
                                ]
                            ],
                            'local_storage' => [
                                'local' => [
                                    'directory' => $config['storages']['local']['directory']
                                ]
                            ],
                            'aws_storage' => [
                                'aws_s3' => [
                                    'service_id' => 'opifer.media.aws_s3.client',
                                    'bucket_name' => $config['storages']['aws_s3']['bucket'],
                                    'options' => [
                                        'directory' => 'originals',
                                        'acl' => 'public-read'
                                    ]
                                ]
                            ]
                        ],
                        'filesystems' => [
                            'tmp_storage' => [
                                'adapter' => 'tmp_storage',
                                'alias'   => 'tmp_storage_filesystem'
                            ],
                            'file_storage' => [
                                'adapter' => $config['default_storage'],
                                'alias'   => 'file_storage_filesystem'
                            ]
                        ],
                        'stream_wrapper' => null
                    ]);
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
        $params = [
            'opifer_media.media_class' => $config['media']['class'],
            'opifer_media.media_index_view' => $config['media']['views']['index'],
            'opifer_media.media_create_view' => $config['media']['views']['create'],
            'opifer_media.media_edit_view' => $config['media']['views']['edit'],
        ];

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
