<?php

namespace Opifer\ExpressionEngine\Prototype;

use Webmozart\Expression\Logic\AndX;

class AndXPrototype extends Prototype
{
    public function __construct($key = 'and', $name = 'AND – match all', $selector = 'and')
    {
        parent::__construct($key, $name, $selector);

        $this->addConstraint(new Choice(AndX::class, 'And'));
        $this->setType(Prototype::TYPE_SET);
    }
}
