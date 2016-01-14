<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DirectoryType extends AbstractType
{
    /** @var string */
    protected $entity;

    /**
     * Constructor
     *
     * @param string $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', 'entity', [
                'label'         => 'directory.parent.label',
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.root', 'ASC')
                        ->addOrderBy('d.lft', 'ASC');
                },
                'class'       => $this->entity,
                'property'    => 'indentedName',
                'required'    => false,
                'empty_value' => 'directory.parent.empty',
                'empty_data'  => null,
                'attr'        => [
                    'help_text' => 'directory.parent.help_text'
                ]
            ])
            ->add('name', 'text', [
                'label' => 'form.name',
                'attr'  => ['help_text' => 'directory.name.help_text']
            ])
            ->add('slug', 'text')
            ->add('save', 'submit', [
                'label' => 'button.submit'
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_directory';
    }
}
