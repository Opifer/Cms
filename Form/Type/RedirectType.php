<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\RedirectBundle\Form\Type\RedirectType as BaseRedirectType;
use Symfony\Component\Form\FormBuilderInterface;

class RedirectType extends BaseRedirectType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('requirements')
            ->add('requirements', 'bootstrap_collection', [
                'type' => 'opifer_requirement',
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
}
