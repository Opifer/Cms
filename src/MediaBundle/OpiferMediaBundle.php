<?php

namespace Opifer\MediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Opifer\MediaBundle\DependencyInjection\Compiler\ProviderCompilerPass;

class OpiferMediaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProviderCompilerPass());
    }
}
