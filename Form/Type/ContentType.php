<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\ContentBundle\Form\DataTransformer\SlugTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentType extends AbstractType
{
    /** @var string */
    protected $directoryClass;

    /** @var object */
    protected $contentManager;

    /**
     * Contstructor
     *
     * @param string $directoryClass
     * @param object $contentManager
     */
    public function __construct($directoryClass, $contentManager)
    {
        $this->directoryClass = $directoryClass;
        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $content = $builder->getData();

        $transformer = new SlugTransformer();

        // Add the default form fields
        $builder
            ->add('title', 'text', [
                'label' => 'form.title',
                'attr'  => [
                    'placeholder' => 'content.form.title.placeholder',
                    'help_text'   => 'content.form.title.help_text',
                ]
            ])
            ->add('description', 'text', [
                'label' => 'form.description',
                'attr'  => [
                    'placeholder' => 'content.form.description.placeholder',
                    'help_text'   => 'content.form.description.help_text',
                ]
            ])
            ->add(
                $builder->create(
                    'slug', 'text', [
                    'attr' => [
                        'placeholder' => 'content.form.slug.placeholder',
                        'help_text'   => 'form.slug.help_text',

                    ]]
                )->addViewTransformer($transformer)
            )
            ->add('directory', 'entity', [
                'class'       => $this->directoryClass,
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.root', 'ASC')
                        ->addOrderBy('d.lft', 'ASC');
                },
                'property'    => 'slug',
                'empty_value' => '/',
                'required'    => false,
                'empty_data'  => null,
                'attr'        => [
                    'help_text' => 'content.form.directory.help_text'
                ]
            ])
            ->add('alias', 'text', [
                'attr'        => [
                    'help_text' => 'content.form.alias.help_text',
                    'widget_col' => 4,
                ]
            ])
            ->add('symlink', 'contentpicker',[
                'label' => 'form.symlink'
            ])
            ->add('active', 'checkbox')
        ;

        $builder->add('valueset', 'opifer_valueset', [
            'attr' => [
                'class' => ($content->getSymlink() ? 'hidden' : '')
            ]
        ]);

        // Add advanced fields only on the advanced option page.
        if ($options['mode'] == 'advanced') {
            $builder->add('realPresentation', 'presentationeditor', [
                'label' => 'form.presentation',
                'attr'  => ['align_with_widget' => true]
            ]);
        }

        $builder->add('save', 'submit', [
            'label' => 'content.form.submit'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'mode' => 'simple',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_content';
    }
}
