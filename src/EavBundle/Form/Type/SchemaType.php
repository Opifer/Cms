<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchemaType extends AbstractType
{
    /** @var string */
    protected $schemaClass;

    /**
     * Constructor
     *
     * @param string              $schemaClass
     */
    public function __construct($schemaClass)
    {
        $this->schemaClass = $schemaClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('attributes', 'bootstrap_collection', [
            'allow_add'    => true,
            'allow_delete' => true,
            'type'         => AttributeType::class
        ]);

        if ($options['object_class'] !== null && class_exists($options['object_class'])) {
            $builder->add('object_class', HiddenType::class, [
                'data' => $options['object_class']
            ]);
        } else {
            $builder->add('object_class', SchemaObjectClassType::class, [
                'label' => 'schema.object_class',
                'attr'  => ['help_text' => 'form.object_class.help_text']
            ]);
        }
    }


    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->schemaClass,
            'validation_groups' => false,
            'object_class' => null
        ]);
    }

    /**
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_eav_schema';
    }
}
