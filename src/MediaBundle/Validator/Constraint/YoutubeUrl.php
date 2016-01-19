<?php

namespace Opifer\MediaBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class YoutubeUrl extends Constraint
{
    public $message = '"%string%" is not a valid Youtube URL';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'Opifer\MediaBundle\Validator\YoutubeUrlValidator';
    }
}
