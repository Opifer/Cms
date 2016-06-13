<?php

namespace Opifer\ExpressionEngine\Prototype;

use AppBundle\Expression\Constraint\IsDayOfWeek;

class DayOfWeekPrototype extends Prototype
{
    public function __construct($name, $selector)
    {
        $this->setName($name);
        $this->setSelector($selector);
        $this->setConstraints([
            new Choice(IsDayOfWeek::class, 'Equals'),
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
