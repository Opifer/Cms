<?php

namespace Opifer\ExpressionEngine\Prototype;

use Webmozart\Expression\Constraint\Equals;
use Webmozart\Expression\Constraint\NotEquals;
use Webmozart\Expression\Constraint\GreaterThan;
use Webmozart\Expression\Constraint\GreaterThanEqual;
use Webmozart\Expression\Constraint\LessThan;
use Webmozart\Expression\Constraint\LessThanEqual;

class NumberPrototype extends Prototype
{
    public function __construct($key, $name, $selector)
    {
        parent::__construct($key, $name, $selector);

        $this->setConstraints([
            new Choice(Equals::class, 'Equals'),
            new Choice(NotEquals::class, 'Not Equals'),
            new Choice(GreaterThan::class, 'Greater than'),
            new Choice(GreaterThanEqual::class, 'Greater than or equals'),
            new Choice(LessThan::class, 'Less than'),
            new Choice(LessThanEqual::class, 'Less than or equals'),
        ]);

        $this->setType(Prototype::TYPE_NUMBER);
    }
}
