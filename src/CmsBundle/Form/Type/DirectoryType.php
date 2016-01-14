<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Form\Type\DirectoryType as ContentDirectoryType;

class DirectoryType extends ContentDirectoryType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $save = $builder->get('save');
        $builder->remove('save');

        $builder
            ->add('searchable', 'checkbox', ['attr' => ['align_with_widget' => true]])
            ->add($save->getName(), $save->getType()->getName(), $save->getOptions())
        ;
    }
}
