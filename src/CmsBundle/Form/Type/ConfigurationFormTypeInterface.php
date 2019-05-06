<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\FormTypeInterface;

interface ConfigurationFormTypeInterface extends FormTypeInterface
{
    /**
     * @return string
     */
    public function getLabel();

    /**
     * Defines the fields on this form type
     *
     * @return array
     */
    public static function getFields();
}
