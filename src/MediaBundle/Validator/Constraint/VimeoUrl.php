<?php

namespace Opifer\MediaBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class VimeoUrl extends Constraint
{
    public $message = '"%string%" is not a valid Vimeo URL';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'Opifer\MediaBundle\Validator\VimeoUrlValidator';
    }
}
