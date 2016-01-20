<?php

namespace Opifer\FormBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type.
 *
 * This formtype defines the form fields necessary to define the form on the event page.
 */
class FormType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('notificationEmail', 'email', [
                'required' => false
            ])
            ->add('redirectUrl', 'text', [
                'required' => false
            ])
            ->add('schema', 'opifer_form_schema')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_form_form';
    }
}
