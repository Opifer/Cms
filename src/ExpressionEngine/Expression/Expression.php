<?php

namespace Opifer\ExpressionEngine\Expression;

use Opifer\ExpressionEngine\Prototype\Prototype;

class Expression implements ExpressionInterface
{
    /** @var string */
    protected $selector;

    /** @var string */
    protected $constraint;

    /** @var string */
    protected $type;

    /** @var mixed */
    protected $value;

    /** @var Expression[] */
    protected $children = [];

    /**
     * @return string
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * @param string $selector
     *
     * @return Expression
     */
    public function setSelector($selector)
    {
        $this->selector = $selector;

        return $this;
    }

    /**
     * @return string
     */
    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * @param string $constraint
     *
     * @return Expression
     */
    public function setConstraint($constraint)
    {
        $this->constraint = $constraint;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->transformValue($this->value);
    }

    /**
     * @param mixed $value
     *
     * @return Expression
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Expression
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param array $children
     *
     * @return Expression
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Transforms the value based on the type
     *
     * @param $value
     * @return \DateTime
     */
    protected function transformValue($value)
    {
        // Override this method to transform values

        return $value;
    }
}
