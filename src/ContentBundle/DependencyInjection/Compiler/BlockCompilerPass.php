<?php

namespace Opifer\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class BlockCompilerPass
 *
 * @package Opifer\ContentBundle\DependencyInjection\Compiler
 */
class BlockCompilerPass implements CompilerPassInterface
{
    /**
     * Process the compiler pass
     *
     * Adds all tagged provider services to the provider pool
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('opifer.content.block_manager')) {
            return;
        }

        $definition = $container->getDefinition('opifer.content.block_manager');
        $taggedServices = $container->findTaggedServiceIds('opifer.content.block_service');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addService',
                    array(new Reference($id), $attributes['alias'])
                );
            }
        }
    }
}
