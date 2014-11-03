<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DirectoryType extends AbstractType
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var string */
    protected $entity;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, $entity)
    {
        $this->translator = $translator;
        $this->entity = $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', 'entity', [
                'label'       => $this->translator->trans('directory.parent.label'),
                'class'       => $this->entity,
                'property'    => 'name',
                'required'    => false,
                'empty_value' => $this->translator->trans('directory.parent.empty'),
                'empty_data'  => null,
                'attr'        => [
                    'help_text' => $this->translator->trans('directory.parent.help_text')
                ]
            ])
            ->add('name', 'text', [
                'label' => $this->translator->trans('form.name'),
                'attr'  => ['help_text' => $this->translator->trans('directory.name.help_text')]
            ])
            ->add('slug', 'text')
            ->add('save', 'submit', [
                'label' => $this->translator->trans('directory.submit')
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_directory';
    }
}
