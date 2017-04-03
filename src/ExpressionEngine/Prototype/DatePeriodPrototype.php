<?php

namespace Opifer\ExpressionEngine\Prototype;

use Opifer\ExpressionEngine\Constraint\DatePeriod;

class DatePeriodPrototype extends Prototype
{
    public function __construct($key, $name, $selector)
    {
        parent::__construct($key, $name, $selector);

        $this->setConstraints([
            new Choice(DatePeriod::class, 'Has'),
        ]);

        $this->setType(Prototype::TYPE_DATE);
    }
}
