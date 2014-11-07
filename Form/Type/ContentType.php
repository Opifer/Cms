<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Opifer\EavBundle\Form\Type\ValueSetType;

class ContentType extends AbstractType
{
    /** @var Symfony\Bundle\FrameworkBundle\Translation\Translator */
    protected $translator;

    /** @var string */
    protected $directoryClass;

    /**
     * Constructor
     *
     * @param Translator $translator
     */
    public function __construct(TranslatorInterface $translator, $directoryClass)
    {
        $this->translator = $translator;
        $this->directoryClass = $directoryClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add the default form fields
        $builder
            ->add('title', 'text', [
                'label' => $this->translator->trans('form.title'),
                'attr'  => [
                    'placeholder' => $this->translator->trans('content.form.title.placeholder')
                ]
            ])
            ->add('description', 'text', [
                'label' => $this->translator->trans('form.description'),
                'attr'  => [
                    'placeholder' => $this->translator->trans('content.form.description.placeholder')
                ]
            ])
            ->add('slug', 'text', [
                'attr' => [
                    'placeholder' => $this->translator->trans('content.form.slug.placeholder'),
                    'help_text'   => $this->translator->trans('form.slug.help_text')
                ]
            ])
            ->add('directory', 'entity', [
                'class'       => $this->directoryClass,
                'property'    => 'name',
                'empty_value' => '/',
                'required'    => false,
                'empty_data'  => null,
                'attr'        => [
                    'help_text' => $this->translator->trans('content.form.directory.help_text')
                ]
            ])
            ->add('valueset', 'opifer_valueset')
        ;

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
