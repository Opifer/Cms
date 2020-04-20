<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\CmsBundle\Entity\Domain;
use Opifer\CmsBundle\Entity\Locale;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('domains', CollectionType::class, [
                'entry_type' => SiteDomainType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('defaultDomain', EntityType::class, [
                'class' => Domain::class,
                'required' => false,
                'query_builder' => function ($qb) use ($options) {
                    return $qb->createQueryBuilder('d')
                        ->where('d.site IS NULL OR d.site = :site')
                        ->setParameter('site', $options['data']->getId());
                },
            ])
            ->add('cookieDomain')
            ->add('defaultLocale', EntityType::class, [
                'label' => 'label.language',
                'class' => Locale::class,
                'choice_label' => 'name',
                'attr' => [
                    'help_text' => 'help.content_language',
                ],
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
