<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ObjectClass form field for schemas
 *
 * Gives the option to choose one of the available objectclasses for schemas.
 */
class SchemaObjectClassType extends AbstractType
{
    protected $entities;

    /**
     * Constructor
     *
     * @param array $entities
     */
    public function __construct(array $entities = array())
    {
        $this->entities = $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = array();
        foreach ($this->entities as $label => $class) {
            $choices[ucfirst($label)] = $class;
        }

        $resolver->setDefaults([
            'choices' => $choices,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'schema_object_class';
    }
}
