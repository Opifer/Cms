<?php

namespace Opifer\ExpressionEngine\Prototype;

use Webmozart\Expression\Logic\OrX;

class OrXPrototype extends Prototype
{
    public function __construct($name, $selector)
    {
        parent::__construct($name, $selector);

        $this->addConstraint(new Choice(OrX::class, 'Or'));

        $this->setType(Prototype::TYPE_SET);
    }
}
