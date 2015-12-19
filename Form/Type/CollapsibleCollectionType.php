<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class CollapsibleCollectionType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'bootstrap_collection';
    }

    /**
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'collapsible_collection';
    }
}
