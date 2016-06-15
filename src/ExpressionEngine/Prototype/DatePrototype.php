<?php

namespace Opifer\ExpressionEngine\Prototype;

use Opifer\ExpressionEngine\Constraint\Date;

class DatePrototype extends Prototype
{
    public function __construct($name, $selector)
    {
        parent::__construct($name, $selector);

        $this->setConstraints([
            new Choice(Date::class, 'Equals'),
        ]);
        $this->setType(Prototype::TYPE_DATE);
    }
}
