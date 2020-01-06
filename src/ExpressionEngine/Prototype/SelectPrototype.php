<?php

namespace Opifer\ExpressionEngine\Prototype;

use Webmozart\Expression\Constraint\Equals;
use Webmozart\Expression\Constraint\NotEquals;

class SelectPrototype extends Prototype
{
    /**
     * SelectPrototype constructor.
     *
     * @param null|string $name
     * @param null|string $selector
     * @param Choice[]    $choices
     */
    public function __construct($key, $name, $selector, $choices = [])
    {
        parent::__construct($key, $name, $selector);

        $this->setChoices($choices);

        $this->setConstraints([
            new Choice(Equals::class, 'Equals'),
            new Choice(NotEquals::class, 'Not Equals'),
        ]);

        $this->setType(Prototype::TYPE_SELECT);
    }
}
