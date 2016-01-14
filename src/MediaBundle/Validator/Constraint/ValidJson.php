<?php

namespace Opifer\MediaBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidJson extends Constraint
{
    public $message = 'The string is not a valid json string';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'Opifer\MediaBundle\Validator\ValidJsonValidator';
    }
}
