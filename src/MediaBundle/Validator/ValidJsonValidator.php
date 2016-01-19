<?php

namespace Opifer\MediaBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidJsonValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is a valid JSON string.
     * If it's already converted by, for example, Doctrine's json_array type,
     * encode the value first and then check if the encode value is valid.
     *
     * @param string|array $value
     * @param Constraint   $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        if (!json_decode($value, true)) {
            $this->context->addViolation(
                $constraint->message,
                array()
            );
        }
    }
}
