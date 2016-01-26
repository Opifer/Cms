<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchemaType extends AbstractType
{
    /** @var AttributeType */
    protected $attributeType;

    /** @var string */
    protected $schemaClass;

    /**
     * Constructor
     *
     * @param AttributeType       $attributeType
     * @param string              $schemaClass
     */
    public function __construct( AttributeType $attributeType, $schemaClass)
    {
        $this->attributeType = $attributeType;
        $this->schemaClass = $schemaClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('displayName', 'text', [
            'label' => 'schema.display_name',
            'attr'  => [
                'class'                  => 'slugify',
                'data-slugify-target'    => '.slugify-target',
                'data-slugify-separator' => '_',
                'placeholder'            => 'form.display_name.placeholder',
                'help_text'              => 'form.display_name.help_text'
            ]
        ])->add('name', 'text', [
            'label' => 'schema.name',
            'attr'  => [
                'class'       => 'slugify-target',
                'placeholder' => 'form.name.placeholder',
                'help_text'   => 'form.name.help_text'
            ]
        ])->add('object_class', 'schema_object_class', [
            'label' => 'schema.object_class',
            'attr'  => [ 'help_text' => 'form.object_class.help_text']
        ])->add('attributes', 'bootstrap_collection', [
            'allow_add'    => true,
            'allow_delete' => true,
            'type'         => $this->attributeType
        ]);
    }


    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'        => $this->schemaClass,
            'validation_groups' => false,
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
        return 'eav_schema';
    }
}
