<?php

namespace Opifer\EavBundle\Form\Transformer;

class AngularAttributeTransformer
{
    /**
     * @param array $options
     *
     * @return array
     */
    public function transform($options)
    {
        $attributes = [];
        if (array_key_exists('ng-model', $options['angular'])) {
            $attributes['ng-model'] = $options['angular']['ng-model'];
        }

        return $attributes;
    }
}
