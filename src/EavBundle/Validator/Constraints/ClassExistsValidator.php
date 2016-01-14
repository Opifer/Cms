<?php

namespace Opifer\EavBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ClassExistsValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is an existing class
     *
     * @param string     $value
     * @param Constraint $constraint
     *
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!class_exists($value)) {
            $this->context->addViolation($constraint->message, []);
        }
    }
}
