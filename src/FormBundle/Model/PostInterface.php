<?php

namespace Opifer\FormBundle\Model;

use Opifer\EavBundle\Model\ValueSetInterface;

interface PostInterface
{
    /**
     * @return FormInterface
     */
    public function getForm();

    /**
     * Get valueSet.
     *
     * @return ValueSetInterface
     */
    public function getValueSet();
}
