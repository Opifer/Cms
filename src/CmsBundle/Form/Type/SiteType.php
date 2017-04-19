<?php

namespace Opifer\CmsBundle\Form\Type;

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
            ->add('domain', EntityType::class, [
                'label' => 'label.domain',
                'class'    => 'OpiferCmsBundle:Domain',
                'property' => 'domain',
                'attr'     => [
                    'help_text'   => 'help_text.site_domain',
                ],
                'required' => true,
                'multiple' => true
            ])
            ->add('cookieDomain')
            ->add('defaultLocale', EntityType::class, [
                'label' => 'label.language',
                'class'    => 'OpiferCmsBundle:Locale',
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
