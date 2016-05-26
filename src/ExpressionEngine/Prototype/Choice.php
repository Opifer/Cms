<?php

namespace Opifer\ExpressionEngine\Prototype;

class Choice
{
    protected $value;
    protected $name;

    public function __construct($value, $name)
    {
        $this->value = $value;
        $this->name = $name;
    }
}
