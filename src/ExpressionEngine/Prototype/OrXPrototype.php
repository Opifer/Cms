<?php

namespace Opifer\ExpressionEngine\Prototype;

use Webmozart\Expression\Logic\OrX;

class OrXPrototype extends Prototype
{
    public function __construct($key = 'or', $name = 'OR - match any', $selector = 'or')
    {
        parent::__construct($key, $name, $selector);

        $this->addConstraint(new Choice(OrX::class, 'Or'));

        $this->setType(Prototype::TYPE_SET);
    }
}
