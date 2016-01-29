<?php

namespace Opifer\EavBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Opifer\CmsBundle\Form\Type\CollapsibleCollectionType;
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

    /**
     * Constructor
     *
     * @param string     $attributeClass
     * @param string     $schemaClass
     */
    public function __construct($attributeClass, $schemaClass)
    {
        $this->attributeClass = $attributeClass;
        $this->schemaClass  = $schemaClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('valueType', ValueProviderType::class, [
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
                // TODO Use Symfony's CollectionType here
                $form->add('options', CollapsibleCollectionType::class, [
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'type'         => OptionType::class
                ]);
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
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'eav_attribute';
    }
}
