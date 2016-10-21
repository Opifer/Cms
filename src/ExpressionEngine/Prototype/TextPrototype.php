<?php

namespace Opifer\ExpressionEngine\Prototype;

use Webmozart\Expression\Constraint\Equals;
use Webmozart\Expression\Constraint\NotEquals;

class TextPrototype extends Prototype
{
    public function __construct($name, $selector)
    {
        parent::__construct($name, $selector);

        $this->setConstraints([
            new Choice(Equals::class, 'Equals'),
            new Choice(NotEquals::class, 'Not Equals'),
            new Choice(Contains::class, 'Contains'),
            new Choice(NotContains::class, 'Not Contains'),
        ]);

        $this->setType(Prototype::TYPE_TEXT);
    }
}
