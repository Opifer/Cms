<?php

namespace Opifer\EavBundle\Form\Type;

use Opifer\EavBundle\Model\TemplateInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TemplateType extends AbstractType
{

    /**
     * @var AttributeType
     */
    protected $attributeType;

    /**
     * @var
     */
    protected $templateClass;


    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     * @param AttributeType       $attributeType
     * @param string              $templateClass
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
            'data_class'        => $this->templateClass,
            'validation_groups' => false,
        ));
    }


    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'eav_template';
    }
}