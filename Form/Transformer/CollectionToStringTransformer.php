<?php

namespace Opifer\MediaBundle\Form\Transformer;

use Doctrine\Common\Collections\Collection;

class CollectionToStringTransformer extends IdsToMediaTransformer
{
    /**
     * {@inheritdoc}
     */
    public function transform($collection)
    {
        if (!$collection instanceof Collection) {
            return '';
        }

        $ids = parent::transform($collection);

        return implode(',', $ids);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string)
    {
        return parent::reverseTransform($string);
    }
}
