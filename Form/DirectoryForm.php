<?php

namespace Opifer\CmsBundle\Form;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DirectoryForm extends AbstractType
{
    /**
     * @var  Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    /**
     * Constructor
     *
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', 'entity', [
                'label'       => $this->translator->trans('directory.parent.label'),
                'class'       => 'Opifer\CmsBundle\Entity\Directory',
                'property'    => 'name',
                'required'    => false,
                'empty_value' => $this->translator->trans('directory.parent.empty'),
                'empty_data'  => null,
                'attr'        => [
                    'help_text' => $this->translator->trans('directory.parent.help_text'),
                ]
            ])
            ->add('name', 'text', [
                'label' => $this->translator->trans('form.name'),
                'attr'  => ['help_text' => $this->translator->trans('directory.name.help_text')]
            ])
            ->add('slug', 'slug')
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
        return 'admin_directory_form';
    }
}
