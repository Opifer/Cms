<?php

namespace Opifer\FormBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Opifer\EavBundle\Form\Type\SchemaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type.
 *
 * This formtype defines the form fields necessary to define the form on the event page.
 */
class FormType extends AbstractType
{
    /** @var EntityManager */
    protected $em;

    /** @var string */
    protected $postClass;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param string        $postClass
     */
    public function __construct(EntityManager $em, $postClass)
    {
        $this->em = $em;
        $this->postClass = $postClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('notificationEmail', EmailType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'example@email.com'
                ]
            ])
            ->add('redirectUrl', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => '/success'
                ]
            ])
            ->add('schema', SchemaType::class, [
                'object_class' => $this->postClass
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_form_form';
    }
}
