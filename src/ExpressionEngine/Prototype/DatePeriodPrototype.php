<?php

namespace Opifer\ExpressionEngine\Prototype;

use Opifer\ExpressionEngine\Constraint\DatePeriod;

class DatePeriodPrototype extends Prototype
{
    public function __construct($name, $selector)
    {
        parent::__construct($name, $selector);

        $this->setConstraints([
            new Choice(DatePeriod::class, 'Has'),
        ]);

        $this->setType(Prototype::TYPE_DATE);
    }
}
