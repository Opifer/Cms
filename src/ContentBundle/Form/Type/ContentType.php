<?php

namespace Opifer\ContentBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Opifer\ContentBundle\Form\DataTransformer\SlugTransformer;
use Opifer\EavBundle\Form\Type\DateTimePickerType;
use Opifer\EavBundle\Form\Type\ValueSetType;
use Opifer\CmsBundle\Entity\ContentType as ContentTypeEntity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

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
        $site = $options['data']->getSite();

        // Add the default form fields
        $builder
            ->add('contentType', EntityType::class, [
                'class' => ContentTypeEntity::class,
                'property' => 'name',
                'required' => true,
            ])
            ->add('template', EntityType::class, [
                'class'    => 'OpiferContentBundle:Template',
                'property' => 'displayName',
                'attr'     => [
                    'help_text' => 'help.template'
                ],
                'required' => false
            ])
            ->add('locale', EntityType::class, [
                'label' => 'label.language',
                'class'    => 'OpiferCmsBundle:Locale',
                'property' => 'name',
                'attr'     => [
                    'help_text'   => 'help.content_language',
                ],
                'required' => false
            ])
            ->add('title', TextType::class, [
                'label' => 'label.title',
                'attr'  => [
                    'placeholder' => 'placeholder.content_title',
                    'help_text'   => 'help.content_title',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('shortTitle', TextType::class, [
                'label' => 'label.short_title',
                'attr'  => [
                    'placeholder' => 'placeholder.content_short_title',
                    'help_text'   => 'help.content_short_title',
                ],
                'required' => false
            ])
            ->add('description', TextType::class, [
                'label' => 'label.description',
                'attr'  => [
                    'placeholder' => 'placeholder.content_description',
                    'help_text'   => 'help.content_description',
                ],
                'required' => false
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
            ->add('publishAt', DateTimePickerType::class, [
                'label' => 'label.publish_at',
                'attr'  => [
                    'help_text' => 'help.publish_at',
                    'class' => 'datetimepicker',
                ],
                'required' => false
            ]);

        if(isset($site)){
            $builder->add('parent', ContentParentType::class, [
                'class' => $this->contentClass,
                'choice_label' => 'title',
                'site' => $site,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($site) {
                    return $er->createQueryBuilder('c')
                        ->where('c.site = :site')
                        ->setParameter('site', $site);
                },
            ]);
        } else {
            $builder->add('parent', ContentParentType::class, [
                'class' => $this->contentClass,
                'choice_label' => 'title',
                'site' => $site,
                'required' => false,
            ]);
        }

        $builder
            ->add('alias', TextType::class, [
                'attr'        => [
                    'help_text' => 'help.alias',
                ],
                'required' => false
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
