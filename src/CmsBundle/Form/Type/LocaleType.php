<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\CmsBundle\Entity\Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType as SymfonyLocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', SymfonyLocaleType::class, ['label' => 'Locale'])
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'help_text' => 'The language name in it\'s own language',
                ],
            ])
            ->add('englishName', TextType::class, [
                'label' => 'English name',
                'attr' => [
                    'help_text' => 'The english language name',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Locale::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_locale';
    }
}
