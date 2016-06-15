<?php

namespace Opifer\ExpressionEngine\Prototype;

class Prototype
{
    const TYPE_SELECT = 'select';
    const TYPE_SET = 'set';
    const TYPE_DATE = 'date';
    const TYPE_NUMBER = 'number';
    const TYPE_TEXT = 'text';

    /**
     * A unique key per type of Prototype. 
     *
     * @var string
     */
    protected $key;

    /**
     * The selector which is evaluated. e.g. the property on a an object.
     *
     * This selector is transformed to a getter when evaluating objects
     *
     * @var string
     */
    protected $selector;

    /**
     * A human readable representation of the prototype
     *
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $constraints;

    /**
     * The type defines the input fields that get rendered in the expression
     *
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $choices;

    /**
     * Constructor.
     *
     * @param string|null $name
     * @param string|null $selector
     */
    public function __construct($name = null, $selector = null)
    {
        if ($name) {
            $this->setName($name);
        }

        if ($selector) {
            $this->setSelector($selector);
        }
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return Prototype
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

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
     * @return Prototype
     */
    public function setSelector($selector)
    {
        $this->selector = $selector;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Prototype
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @param Choice[] $constraints
     *
     * @return Prototype
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * @param Choice $constraint
     *
     * @return Prototype
     */
    public function addConstraint(Choice $constraint)
    {
        $this->constraints[] = $constraint;

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
     * @return Prototype
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param array $choices
     *
     * @return Prototype
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * @param Choice $choice
     *
     * @return Prototype
     */
    public function addChoice(Choice $choice)
    {
        $this->choices[] = $choice;

        return $this;
    }
}
