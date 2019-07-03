<?php

namespace Opifer\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Password extends Constraint
{
    public $message = 'Password must contain at least 1 nummeric, 1 capital and 1 lowercase.';
}
