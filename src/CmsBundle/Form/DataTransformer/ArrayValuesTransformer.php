<?php

namespace Opifer\CmsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayValuesTransformer implements DataTransformerInterface
{
    public function transform($array)
    {
        return $array;
    }

    public function reverseTransform($array)
    {
        // Always convert the values to a simple array, since in some cases the values might be passed with custom
        // keys, which is then converted to an object on json_encoding.
        return array_values($array);
    }
}
