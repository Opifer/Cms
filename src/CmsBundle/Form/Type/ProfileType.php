<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('company', TextType::class, ['required' => false])
            ->add('jobPosition', TextType::class, ['required' => false])
            ->add('email', TextType::class)
            ->add('plainPassword', RepeatedType::class, [
                'required' => ($options['data']->getId()) ? false : true,
                'type' => PasswordType::class,
                'first_options' => ['label' => 'form.password'],
                'second_options' => ['label' => 'form.password_confirmation'],
                'invalid_message' => 'fos_user.password.mismatch',
            ])
            ->add('avatar', MediaPickerType::class, [
                'multiple' => false,
            ])
        ;
    }
}
