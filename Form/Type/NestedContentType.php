<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Nested content form type
 */
class NestedContentType extends AbstractType
{
    const SEPARATOR = '__';

    /** @var string */
    protected $name;

    /**
     * Constructor
     *
     * We need to create unique form names to differentiate the filled in form types.
     * If we don't do this, it's not possible to create multiple forms of the same
     * type on one angular page.
     *
     * @param string         $attribute
     * @param integer|string $id
     */
    public function __construct($attribute, $id)
    {
        if (strpos($id, 'nested_content') !== false) {
            $this->name = $id;
        } else {
            $this->name = 'nested_content' . self::SEPARATOR . $attribute . self::SEPARATOR . $id;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
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
