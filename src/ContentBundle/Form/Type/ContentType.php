<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\ContentBundle\Form\DataTransformer\SlugTransformer;
use Opifer\EavBundle\Form\Type\DateTimePickerType;
use Opifer\EavBundle\Form\Type\ValueSetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Content Form Type
 */
class ContentType extends AbstractType
{
    /** @var string */
    private $contentClass;

    /**
     * Constructor.
     *
     * @param string $contentClass
     */
    public function __construct($contentClass)
    {
        $this->contentClass = $contentClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add the default form fields
        $builder
            ->add('template', EntityType::class, [
                'class'    => 'OpiferContentBundle:Template',
                'property' => 'displayName',
                'attr'     => [
                    'help_text' => 'help.template'
                ],
            ])
            ->add('locale', EntityType::class, [
                'label' => 'label.language',
                'class'    => 'OpiferCmsBundle:Locale',
                'property' => 'name',
                'attr'     => [
                    'help_text'   => 'help.content_language',
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'label.title',
                'attr'  => [
                    'placeholder' => 'placeholder.content_title',
                    'help_text'   => 'help.content_title',
                ]
            ])
            ->add('shortTitle', TextType::class, [
                'label' => 'label.short_title',
                'attr'  => [
                    'placeholder' => 'placeholder.content_short_title',
                    'help_text'   => 'help.content_short_title',
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'label.description',
                'attr'  => [
                    'placeholder' => 'placeholder.content_description',
                    'help_text'   => 'help.content_description',
                ]
            ])
            ->add(
                $builder->create(
                    'slug', TextType::class, [
                        'attr' => [
                            'placeholder' => 'placeholder.slug',
                            'help_text'   => 'help.slug',
                        ]
                    ]
                )->addViewTransformer(new SlugTransformer())
            )
            ->add('publish_at', DateTimePickerType::class, [
                'label' => 'label.publish_at',
                'attr'  => [
                    'help_text'   => 'help.publish_at',
                ]
            ])
            ->add('parent', ContentParentType::class, [
                'class' => $this->contentClass,
                'choice_label' => 'title',
                'required' => false,
            ])
            ->add('alias', TextType::class, [
                'attr'        => [
                    'help_text' => 'help.alias',
                ]
            ])
            ->add('active', CheckboxType::class, [
                'attr' => [
                    'align_with_widget' => true,
                    'help_text' => 'help.active'
                ],
            ])
            ->add('indexable', CheckboxType::class, [
                'label' => 'label.indexable',
                'attr' => [
                    'align_with_widget' => true,
                    'class' => 'before-form-section',
                    'help_text' => 'help.indexable',
                ],
            ])
            ->add('searchable', CheckboxType::class, [
                'label' => 'label.searchable',
                'attr' => [
                    'align_with_widget' => true,
                    'class' => 'before-form-section',
                    'help_text' => 'help.searchable',
                ],
            ])
            ->add('showInNavigation', CheckboxType::class, [
                'attr' => [
                    'align_with_widget' => true,
                    'help_text' => 'help.show_in_navigation'
                ],
            ])
        ;

        // Only add the ValueSetType if a ContentType is set, to avoid persisting empty valuesets.
        if ($options['data']->getValueSet()) {
            $builder->add('valueset', ValueSetType::class);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_content_details';
    }
}
