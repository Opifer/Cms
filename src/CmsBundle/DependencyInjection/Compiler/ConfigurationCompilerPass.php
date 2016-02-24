<?php

namespace Opifer\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigurationCompilerPass implements CompilerPassInterface
{
    /**
     * Process the compiler pass.
     *
     * Adds all tagged forms to the ConfigurationRegistry
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('opifer.cms.configuration_form_registry')) {
            return;
        }

        $definition = $container->getDefinition('opifer.cms.configuration_form_registry');
        $taggedServices = $container->findTaggedServiceIds('opifer.configuration_form');

        foreach ($taggedServices as $id => $tagAttributes) {
            $definition->addMethodCall('addForm', [new Reference($id)]);
        }
    }
}
