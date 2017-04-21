<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\CmsBundle\Entity\Domain;
use Opifer\CmsBundle\Entity\Locale;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('domains', EntityType::class, [
                'label' => 'label.domain',
                'class' => Domain::class,
                'attr'  => [
                    'help_text'   => 'help_text.site_domain',
                ],
                'required' => true,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('cookieDomain')
            ->add('defaultLocale', EntityType::class, [
                'label' => 'label.language',
                'class'    => Locale::class,
                'property' => 'name',
                'attr'     => [
                    'help_text'   => 'help.content_language',
                ],
                'required' => true
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_cms_site';
    }
}
