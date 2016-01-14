<?php

namespace Opifer\ContentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Opifer\ContentBundle\DependencyInjection\Compiler\BlockCompilerPass;

class OpiferContentBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new BlockCompilerPass());
    }
}
