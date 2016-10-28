<?php

namespace Opifer\ExpressionEngine\Prototype;

use Webmozart\Expression\Constraint\Equals;
use Webmozart\Expression\Constraint\GreaterThan;
use Webmozart\Expression\Constraint\GreaterThanEqual;
use Webmozart\Expression\Constraint\LessThan;
use Webmozart\Expression\Constraint\LessThanEqual;

class DatePrototype extends Prototype
{
    public function __construct($name, $selector)
    {
        parent::__construct($name, $selector);

        $this->setConstraints([
            new Choice(Equals::class, 'Equals'),
            new Choice(GreaterThan::class, 'Greater than'),
            new Choice(GreaterThanEqual::class, 'Greater than or equal'),
            new Choice(LessThan::class, 'Less than'),
            new Choice(LessThanEqual::class, 'Less than or equal'),
        ]);
        $this->setType(Prototype::TYPE_DATE);
    }
}
