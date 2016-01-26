<?php

namespace Opifer\EavBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeType extends AbstractType
{
    /** @var string */
    protected $attributeClass;

    /** @var string */
    protected $schemaClass;

    /** @var OptionType */
    protected $optionType;

    /**
     * Constructor
     *
     * @param OptionType $optionType
     * @param string     $attributeClass
     * @param string     $schemaClass
     */
    public function __construct(OptionType $optionType, $attributeClass, $schemaClass)
    {
        $this->optionType     = $optionType;
        $this->attributeClass = $attributeClass;
        $this->schemaClass  = $schemaClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('valueType', 'value_provider', [
            'label' => 'attribute.value_type',
            'attr'  => [
                'placeholder' => 'form.value_type.placeholder',
                'help_text'   => 'form.value_type.help_text'
            ]
        ])->add('displayName', TextType::class, [
            'label' => 'attribute.display_name',
            'attr'  => [
                'class'                  => 'slugify',
                'data-slugify-target'    => '.slugify-target-' . $builder->getName(),
                'data-slugify-separator' => '_',
                'placeholder'            => 'form.display_name.placeholder',
                'help_text'              => 'form.display_name.help_text',
                'widget_col' => 6
            ]
        ])->add('name', TextType::class, [
            'label' => 'attribute.name',
            'attr'  => [
                'class'       => 'slugify-target-' . $builder->getName(),
                'placeholder' => 'form.name.placeholder',
                'help_text'   => 'form.name.help_text',
                'widget_col' => 6
            ]
        ])->add('description', TextType::class, [
            'required' => false,
            'label' => 'attribute.description',
            'attr'  => [ 'help_text' => 'form.description.help_text' ]
        ])->add('sort', IntegerType::class, [
            'label' => 'attribute.sort',
            'attr'  => [ 'help_text' => 'form.sort.help_text', 'widget_col' => 2 ],
            'empty_data' => 0,
            'required' => false
        ])->add('required', ChoiceType::class, [
            'choices' => [
                false => 'Not required',
                true => 'Required'
            ],
            'label' => 'Required',
            'required' => true
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $attribute = $event->getData();
            $form = $event->getForm();

            if ($attribute && in_array($attribute->getValueType(), ['checklist', 'select', 'radio'])) {
                $form->add('options', 'collapsible_collection', [
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'type'         => $this->optionType
                ]);
            }

            if ($attribute && $attribute->getValueType() == 'nested') {
                $form->add(
                    'allowedSchemas',
                    'entity',
                    [
                        'class' => $this->schemaClass,
                        'property' => 'displayName',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('t')
                                ->orderBy('t.displayName', 'ASC');
                        },
                        'by_reference' => false,
                        'expanded' => true,
                        'multiple' => true,
                        'label' => $this->translator->trans('attribute.allowed_schemas'),
                        'attr' => ['help_text' => $this->translator->trans('form.allowed_schemas.help_text')]
                    ]
                );
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->attributeClass,
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
        return 'eav_attribute';
    }
}
