<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;
use Opifer\EavBundle\Entity\OptionValue;

class AttributeType extends AbstractType
{
    /**
     * @var  Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $attributeClass;

    /**
     * @var string
     */
    protected $templateClass;

    protected $optionType;


    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     * @param OptionType          $optionType
     * @param string              $attributeClass
     * @param string              $templateClass
     */
    public function __construct(TranslatorInterface $translator, OptionType $optionType, $attributeClass, $templateClass)
    {
        $this->translator     = $translator;
        $this->optionType     = $optionType;
        $this->attributeClass = $attributeClass;
        $this->templateClass  = $templateClass;
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
                'help_text'              => $this->translator->trans('form.display_name.help_text'),
                'widget_col' => 6
            ]
        ])->add('name', 'text', [
            'label' => $this->translator->trans('attribute.name'),
            'attr'  => [
                'class'       => 'slugify-target-__name__',
                'placeholder' => $this->translator->trans('form.name.placeholder'),
                'help_text'   => $this->translator->trans('form.name.help_text'),
                'widget_col' => 6
            ]
        ])->add('description', 'text', [
            'label' => $this->translator->trans('attribute.description'),
            'attr'  => [ 'help_text' => $this->translator->trans('form.description.help_text') ]
        ])->add('sort', 'integer', [
            'label' => $this->translator->trans('attribute.sort'),
            'attr'  => [ 'help_text' => $this->translator->trans('form.sort.help_text'), 'widget_col' => 2 ]
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $attribute = $event->getData();
            $form = $event->getForm();

            if ($attribute && in_array($attribute->getValueType(), ['checklist', 'select'])) {
                $form->add('options', 'collapsible_collection', [
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'type'         => $this->optionType
                ]);
            }

            if ($attribute && $attribute->getValueType() == 'nested') {
                $form->add(
                    'allowedTemplates',
                    'entity',
                    [
                        'class' => $this->templateClass,
                        'property' => 'displayName',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('t')
                                ->orderBy('t.displayName', 'ASC');
                        },
                        'by_reference' => false,
                        'expanded' => true,
                        'multiple' => true,
                        'label' => $this->translator->trans('attribute.allowed_templates'),
                        'attr' => ['help_text' => $this->translator->trans('form.allowed_templates.help_text')]
                    ]
                );
            }
        });
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