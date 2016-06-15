<?php

namespace Opifer\ExpressionEngine\Prototype;

use Webmozart\Expression\Constraint\Equals;
use Webmozart\Expression\Constraint\NotEquals;

class NumberPrototype extends Prototype
{
    public function __construct($name, $selector)
    {
        parent::__construct($name, $selector);

        $this->setConstraints([
            new Choice(Equals::class, 'Equals'),
            new Choice(NotEquals::class, 'Not Equals'),
        ]);

        $this->setType(Prototype::TYPE_NUMBER);
    }
}
