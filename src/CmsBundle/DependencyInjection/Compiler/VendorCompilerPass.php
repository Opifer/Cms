<?php

namespace Opifer\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class VendorCompilerPass implements CompilerPassInterface
{
    /**
     * Process the compiler pass.
     *
     * Updates configuration for bundles that do not offer abilities to override services.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('lexik_translation.locale.manager')) {
            // Adds the entity manager on the translation locale, so we can get retrieve the locales from the database.
            $container->findDefinition('lexik_translation.locale.manager')
                ->addMethodCall('setEntityManager', [new Reference('doctrine.orm.entity_manager')]);
        }
    }
}
