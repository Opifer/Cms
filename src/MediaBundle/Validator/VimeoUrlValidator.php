<?php

namespace Opifer\MediaBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VimeoUrlValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $regex = '/(?<=v(\=|\/))([-a-zA-Z0-9_]+)|(?<=vimeo\.com\/)([-a-zA-Z0-9_]+)/';

        if (!preg_match($regex, $value, $matches)) {
            $this->context->addViolation($constraint->message, ['%string%' => $value]);
        }
    }
}
