<?php


namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AttributeType extends AbstractType
{

    /**
     * @var  Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    /**
     * @var
     */
    protected $attributeClass;

    protected $optionType;


    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     * @param OptionType          $optionType
     * @param                     $attributeClass
     */
    public function __construct(TranslatorInterface $translator, OptionType $optionType, $attributeClass)
    {
        $this->translator     = $translator;
        $this->optionType     = $optionType;
        $this->attributeClass = $attributeClass;
    }


    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('valueType', 'value_provider', [
            'label' => $this->translator->trans('attribute.value_type'),
            'attr'  => [
                'placeholder' => $this->translator->trans('form.value_type.placeholder'),
                'help_text'   => $this->translator->trans('form.value_type.help_text')
            ]
        ])->add('displayName', 'text', [
            'label' => $this->translator->trans('attribute.display_name'),
            'attr'  => [
                'class'                  => 'slugify',
                'data-slugify-target'    => '.slugify-target-__name__',
                'data-slugify-separator' => '_',
                'placeholder'            => $this->translator->trans('form.display_name.placeholder'),
                'help_text'              => $this->translator->trans('form.display_name.help_text')
            ]
        ])->add('name', 'text', [
            'label' => $this->translator->trans('attribute.name'),
            'attr'  => [
                'class'       => 'slugify-target-__name__',
                'placeholder' => $this->translator->trans('form.name.placeholder'),
                'help_text'   => $this->translator->trans('form.name.help_text')
            ]
        ])->add('description', 'text', [
            'label' => $this->translator->trans('attribute.description'),
            'attr'  => [ 'help_text' => $this->translator->trans('form.description.help_text') ]
        ])->add('sort', 'integer', [
            'label' => $this->translator->trans('attribute.sort'),
            'attr'  => [ 'help_text' => $this->translator->trans('form.sort.help_text') ]
        ])->add('options', 'collapsible_collection', [
            'allow_add'    => true,
            'allow_delete' => true,
            'type'         => $this->optionType
        ]);
    }


    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->attributeClass,
        ));
    }


    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'eav_attribute';
    }
}