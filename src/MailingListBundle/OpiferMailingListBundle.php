<?php

namespace Opifer\MailingListBundle;

use Opifer\MailingListBundle\DependencyInjection\Compiler\ProviderPoolCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OpiferMailingListBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProviderPoolCompilerPass());
    }
}
