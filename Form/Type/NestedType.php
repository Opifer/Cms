<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Nested form type
 */
class NestedType extends AbstractType
{
    const SEPARATOR = '__';
    const PREFIX = 'nested_content';

    /** @var string */
    protected $name;

    /**
     * Constructor
     *
     * We need to create unique form names to differentiate the filled in form types.
     * If we don't do this, it's not possible to create multiple forms of the same
     * type on one angular page.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('valueset', 'opifer_valueset')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
