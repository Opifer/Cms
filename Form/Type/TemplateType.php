<?php

namespace Opifer\EavBundle\Form\Type;

use Opifer\EavBundle\Model\TemplateInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TemplateType extends AbstractType
{

    /**
     * @var  Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

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
    public function __construct(TranslatorInterface $translator, AttributeType $attributeType, $templateClass)
    {
        $this->translator    = $translator;
        $this->attributeType = $attributeType;
        $this->templateClass = $templateClass;
    }


    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('displayName', 'text', [
            'label' => $this->translator->trans('template.display_name'),
            'attr'  => [
                'class'                  => 'slugify',
                'data-slugify-target'    => '.slugify-target',
                'data-slugify-separator' => '_',
                'placeholder'            => $this->translator->trans('form.display_name.placeholder'),
                'help_text'              => $this->translator->trans('form.display_name.help_text')
            ]
        ])->add('name', 'text', [
            'label' => $this->translator->trans('template.name'),
            'attr'  => [
                'class'       => 'slugify-target',
                'placeholder' => $this->translator->trans('form.name.placeholder'),
                'help_text'   => $this->translator->trans('form.name.help_text')
            ]
        ])->add('object_class', 'template_object_class', [
            'label' => $this->translator->trans('template.object_class'),
            'attr'  => [ 'help_text' => $this->translator->trans('form.object_class.help_text') ]
        ])->add('presentation', 'presentationeditor', [
            'label' => $this->translator->trans('template.presentation'),
            'attr'  => [ 'help_text' => $this->translator->trans('form.presentation.help_text') ]
        ])->add('attributes', 'bootstrap_collection', [
            'allow_add'    => true,
            'allow_delete' => true,
            'type'         => $this->attributeType
        ])->add('save', 'submit', [
            'label' => ucfirst($this->translator->trans('form.submit'))
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