<?php

namespace Opifer\EavBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ValueCompilerPass implements CompilerPassInterface
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
        if (!$container->hasDefinition('opifer.eav.value_provider.pool')) {
            return;
        }

        $definition = $container->getDefinition('opifer.eav.value_provider.pool');
        $taggedServices = $container->findTaggedServiceIds('opifer.eav.value_provider');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addValue',
                    array(new Reference($id), $attributes['alias'])
                );
            }
        }
    }
}
