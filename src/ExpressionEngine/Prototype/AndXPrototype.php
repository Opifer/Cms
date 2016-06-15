<?php

namespace Opifer\ExpressionEngine\Prototype;

use Webmozart\Expression\Logic\AndX;

class AndXPrototype extends Prototype
{
    public function __construct($name, $selector)
    {
        parent::__construct($name, $selector);

        $this->addConstraint(new Choice(AndX::class, 'And'));
        $this->setType(Prototype::TYPE_SET);
    }
}
