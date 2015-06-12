<?php

namespace Opifer\CmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OpiferCmsBundle extends Bundle
{
    /**
     * Set FOSUserBundle as this bundle's parent to easily override it's files
     *
     * @return string
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
