<?php

namespace Opifer\ExpressionEngine\Expression;

interface ExpressionInterface
{
    public function getSelector();
    public function getConstraint();
    public function getValue();
}
