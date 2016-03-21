<?php

namespace Opifer\MailingListBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Subscribe Type
 */
class SubscribeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'label'    => 'label.email',
                'attr'  => [
                    'placeholder' => 'placeholder.email',
                ]
            ])
        ;
    }
}
