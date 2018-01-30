<?php

namespace Opifer\ExpressionEngine\Prototype;

use Opifer\ExpressionEngine\Constraint\DayOfWeek;

class DayOfWeekPrototype extends Prototype
{
    public function __construct($key, $name, $selector)
    {
        parent::__construct($key, $name, $selector);

        $this->setConstraints([
            new Choice(DayOfWeek::class, 'Equals'),
        ]);
        $this->setType(Prototype::TYPE_SELECT);
        $this->setChoices([
            new Choice('monday', 'Monday'),
            new Choice('tuesday', 'Tuesday'),
            new Choice('wednesday', 'Wednesday'),
            new Choice('thursday', 'Thursday'),
            new Choice('friday', 'Friday'),
            new Choice('saturday', 'Saturday'),
            new Choice('sunday', 'Sunday'),
        ]);
    }
}
