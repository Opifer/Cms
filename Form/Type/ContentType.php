<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use Opifer\EavBundle\Form\Type\ValueSetType;
use Opifer\ContentBundle\Form\DataTransformer\SlugTransformer;
use Opifer\ContentBundle\Form\DataTransformer\IdToContentTransformer;

class ContentType extends AbstractType
{
    /** @var Symfony\Bundle\FrameworkBundle\Translation\Translator */
    protected $translator;

    /** @var string */
    protected $directoryClass;

    /** @var object */
    protected $contentManager;

    /**
     * Contstructor
     * 
     * @param TranslatorInterface $translator
     * @param string $directoryClass
     * @param object $contentManager
     */
    public function __construct(TranslatorInterface $translator, $directoryClass, $contentManager)
    {
        $this->translator = $translator;
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
                'label' => $this->translator->trans('form.title'),
                'attr'  => [
                    'placeholder' => $this->translator->trans('content.form.title.placeholder'),
                    'help_text'   => $this->translator->trans('content.form.title.help_text'),
                ]
            ])
            ->add('description', 'text', [
                'label' => $this->translator->trans('form.description'),
                'attr'  => [
                    'placeholder' => $this->translator->trans('content.form.description.placeholder'),
                    'help_text'   => $this->translator->trans('content.form.description.help_text'),
                ]
            ])
            ->add(
                $builder->create(
                    'slug', 'text', [
                    'attr' => [
                        'placeholder' => $this->translator->trans('content.form.slug.placeholder'),
                        'help_text'   => $this->translator->trans('form.slug.help_text'),

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
                    'help_text' => $this->translator->trans('content.form.directory.help_text')
                ]
            ])
            ->add('alias', 'text', [
                'attr'        => [
                    'help_text' => $this->translator->trans('content.form.alias.help_text'),
                    'widget_col' => 4,
                ]
            ])
            ->add('symlink', 'contentpicker',[
                'label' => $this->translator->trans('form.symlink')
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
                'label' => $this->translator->trans('form.presentation'),
                'attr'  => ['align_with_widget' => true]
            ]);
        }

        $builder->add('save', 'submit', [
            'label' => $this->translator->trans('content.form.submit')
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
