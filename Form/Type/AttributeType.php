<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;

class AttributeType extends AbstractType
{
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
     * @param OptionType          $optionType
     * @param string              $attributeClass
     * @param string              $templateClass
     */
    public function __construct(OptionType $optionType, $attributeClass, $templateClass)
    {
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
            'label' => 'attribute.value_type',
            'attr'  => [
                'placeholder' => 'form.value_type.placeholder',
                'help_text'   => 'form.value_type.help_text'
            ]
        ])->add('displayName', 'text', [
            'label' => 'attribute.display_name',
            'attr'  => [
                'class'                  => 'slugify',
                'data-slugify-target'    => '.slugify-target-' . $builder->getName(),
                'data-slugify-separator' => '_',
                'placeholder'            => 'form.display_name.placeholder',
                'help_text'              => 'form.display_name.help_text',
                'widget_col' => 6
            ]
        ])->add('name', 'text', [
            'label' => 'attribute.name',
            'attr'  => [
                'class'       => 'slugify-target-' . $builder->getName(),
                'placeholder' => 'form.name.placeholder',
                'help_text'   => 'form.name.help_text',
                'widget_col' => 6
            ]
        ])->add('description', 'text', [
            'required' => false,
            'label' => 'attribute.description',
            'attr'  => [ 'help_text' => 'form.description.help_text' ]
        ])->add('sort', 'integer', [
            'label' => 'attribute.sort',
            'attr'  => [ 'help_text' => 'form.sort.help_text', 'widget_col' => 2 ],
            'empty_data' => 0
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
                        'label' => 'attribute.allowed_templates',
                        'attr' => ['help_text' => 'form.allowed_templates.help_text']
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