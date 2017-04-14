<?php

namespace Opifer\CmsBundle;

use Opifer\CmsBundle\DependencyInjection\Compiler\ConfigurationCompilerPass;
use Opifer\CmsBundle\DependencyInjection\Compiler\VendorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OpiferCmsBundle extends Bundle
{
    /**
     * Registers the compiler passes
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ConfigurationCompilerPass());
        $container->addCompilerPass(new VendorCompilerPass());
    }

    /**
     * Set FOSUserBundle as this bundle's parent to easily override it's files.
     *
     * @return string
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
