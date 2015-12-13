<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplateType extends AbstractType
{
    /** @var AttributeType */
    protected $attributeType;

    /** @var string */
    protected $templateClass;

    /**
     * Constructor
     *
     * @param AttributeType $attributeType
     * @param string        $templateClass
     */
    public function __construct( AttributeType $attributeType, $templateClass)
    {
        $this->attributeType = $attributeType;
        $this->templateClass = $templateClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('displayName', 'text', [
            'label' => 'template.display_name',
            'attr'  => [
                'class'                  => 'slugify',
                'data-slugify-target'    => '.slugify-target',
                'data-slugify-separator' => '_',
                'placeholder'            => 'form.display_name.placeholder',
                'help_text'              => 'form.display_name.help_text'
            ]
        ])->add('name', 'text', [
            'label' => 'template.name',
            'attr'  => [
                'class'       => 'slugify-target',
                'placeholder' => 'form.name.placeholder',
                'help_text'   => 'form.name.help_text'
            ]
        ])->add('object_class', 'template_object_class', [
            'label' => 'template.object_class',
            'attr'  => [ 'help_text' => 'form.object_class.help_text']
        ])->add('presentation', 'presentationeditor', [
            'label' => 'template.presentation',
            'attr'  => [ 'help_text' => 'form.presentation.help_text']
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
            'data_class'        => $this->templateClass,
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
        return 'eav_template';
    }
}
