<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\EavBundle\Form\Type\ValueSetType;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Content Form Type
 */
class LayoutType extends AbstractType
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add the default form fields
        $builder
            ->add('template', EntityType::class, [
                'class'    => 'OpiferContentBundle:Template',
                'choice_label' => 'displayName',
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
            ->add('active', HiddenType::class, [
                'attr' => [
                    'value' => 0
                ],
            ])
            ->add('indexable', HiddenType::class, [
                'label' => 'label.indexable',
                'attr' => [
                    'value' => 0
                ],
            ])
            ->add('searchable', HiddenType::class, [
                'attr' => [
                    'value' => 0
                ],
            ])
            ->add('showInNavigation', HiddenType::class, [
                'attr' => [
                    'value' => 0
                ],
            ])
            ->add('layout', HiddenType::class, [
                'attr' => [
                    'value' => 1
                ],
            ])
            ->add('preview', MediaPickerType::class, [
                'multiple' => false,
            ])
        ;

        // Only add the ValueSetType if a ContentType is set, to avoid persisting empty valuesets.
        if ($options['data']->getValueSet()) {
            $builder->add('valueset', ValueSetType::class);
        }
    }
}
