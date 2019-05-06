<?php

namespace Opifer\MediaBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VimeoUrlValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $regex = '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/';

        if (!preg_match($regex, $value, $matches)) {
            $this->context->addViolation($constraint->message, ['%string%' => $value]);
        }
    }
}
