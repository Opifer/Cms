<?php

namespace Opifer\CmsBundle\ValueProvider;

use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class FormValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    protected $formClass;

    /**
     * @param $formClass
     */
    public function __construct($formClass)
    {
        $this->formClass = $formClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('form', 'entity', [
            'empty_value'   => '-- None --',
            'expanded'      => false,
            'multiple'      => false,
            'class'         => $this->formClass,
            'property'      => 'name',
            //'query_builder' => function (EntityRepository $er) {
            //    return $er->createQueryBuilder('t')
            //        ->where('t.objectClass = :objectClass')
            //        ->setParameter('objectClass', 'Opifer\CmsBundle\Entity\Post');
            //}
        ]);
        //$builder->add('value', 'text', [
        //    'label' => 'Success page',
        //    'attr' => [
        //        'placeholder' => 'The URL the form has to redirect to after success',
        //    ]
        //]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\CmsBundle\Entity\FormValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Form';
    }
}
