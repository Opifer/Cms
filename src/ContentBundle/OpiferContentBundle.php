<?php

namespace Opifer\ContentBundle;

use Opifer\ContentBundle\DependencyInjection\Compiler\BlockCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OpiferContentBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new BlockCompilerPass());
    }
}
