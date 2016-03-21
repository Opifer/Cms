<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\ContentBundle\Form\DataTransformer\SlugTransformer;
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
            ->add('title', TextType::class, [
                'label' => 'label.title',
                'attr'  => [
                    'placeholder' => 'placeholder.content_title',
                    'help_text'   => 'help.content_title',
                ]
            ])
            ->add('navTitle', TextType::class, [
                'label' => 'label.nav_title',
                'attr'  => [
                    'placeholder' => 'placeholder.content_nav_title',
                    'help_text'   => 'help.content_nav_title',
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
