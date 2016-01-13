<?php

namespace Opifer\EavBundle;

use Opifer\EavBundle\DependencyInjection\Compiler\ValueCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OpiferEavBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ValueCompilerPass());
    }
}
