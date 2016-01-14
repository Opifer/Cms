<?php

namespace Opifer\EavBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ClassExists extends Constraint
{
    public $message = 'The passed value must be an existing class';
}
