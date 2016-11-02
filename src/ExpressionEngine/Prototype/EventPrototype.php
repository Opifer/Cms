<?php

namespace Opifer\ExpressionEngine\Prototype;

use Webmozart\Expression\Constraint\Equals;
use Webmozart\Expression\Constraint\NotEquals;

class EventPrototype extends Prototype
{
    public function __construct($key, $name, $selector)
    {
        parent::__construct($key, $name, $selector);

        // $this->setConstraints([
        //     new Choice(Equals::class, 'Equals'),
        //     new Choice(NotEquals::class, 'Not Equals'),
        //     new Choice(Contains::class, 'Contains'),
        //     new Choice(NotContains::class, 'Not Contains'),
        // ]);

        $this->setType(Prototype::TYPE_EVENT);
    }
}
