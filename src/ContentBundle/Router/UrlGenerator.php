<?php

namespace Opifer\ContentBundle\Router;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

/**
 * Content UrlGenerator.
 */
class UrlGenerator extends BaseUrlGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $url = parent::generate($name, $parameters, $referenceType);

        if(substr($url, -6) == '/index') {
            $url = rtrim($url, "index");
        }

        return $url;
    }
}