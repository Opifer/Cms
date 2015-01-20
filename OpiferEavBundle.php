<?php

namespace Opifer\EavBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Opifer\EavBundle\DependencyInjection\Compiler\ValueCompilerPass;

class OpiferEavBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ValueCompilerPass());
    }
}
