<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class FormValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    protected $schemaClass;

    public function __construct($schemaClass)
    {
        $this->schemaClass = $schemaClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('schema', 'entity', [
            'empty_value'   => '-- None --',
            'expanded'      => false,
            'multiple'      => false,
            'class'         => $this->schemaClass,
            'property'      => 'displayName',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('t')
                    ->where('t.objectClass = :objectClass')
                    ->setParameter('objectClass', 'Opifer\CmsBundle\Entity\Post');
            }
        ]);
        $builder->add('value', 'text', [
            'label' => 'Success page',
            'attr' => [
                'placeholder' => 'The URL the form has to redirect to after success',
            ]
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\FormValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Form';
    }
}
