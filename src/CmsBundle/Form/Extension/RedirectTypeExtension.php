<?php

namespace Opifer\CmsBundle\Form\Extension;

use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;
use Opifer\RedirectBundle\Form\Type\RedirectType;
use Opifer\RedirectBundle\Form\Type\RequirementType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class RedirectTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('requirements')
            ->add('requirements', BootstrapCollectionType::class, [
                'entry_type' => RequirementType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'opifer_redirect.form.requirements.label',
                'attr' => [
                    'help_text' => 'opifer_redirect.form.requirements.help_text',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return RedirectType::class;
    }
}
