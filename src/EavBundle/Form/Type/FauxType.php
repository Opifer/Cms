<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class FauxType extends AbstractType
{
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
        return 'eav_faux';
    }
}
