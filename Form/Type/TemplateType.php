<?php

namespace Opifer\EavBundle\Form\Type;

use Opifer\EavBundle\Model\SchemaInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SchemaType extends AbstractType
{

    /**
     * @var AttributeType
     */
    protected $attributeType;

    /**
     * @var
     */
    protected $schemaClass;


    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
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
        ])->add('presentation', 'presentationeditor', [
            'label' => 'schema.presentation',
            'attr'  => [ 'help_text' => 'form.presentation.help_text']
        ])->add('attributes', 'bootstrap_collection', [
            'allow_add'    => true,
            'allow_delete' => true,
            'type'         => $this->attributeType
        ])->add('save', 'submit', [
            'label' => ucfirst('form.submit')
        ]);
    }


    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => $this->schemaClass,
            'validation_groups' => false,
        ));
    }


    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'eav_schema';
    }
}