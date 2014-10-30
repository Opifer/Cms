<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class FauxType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'eav_faux';
    }
}
