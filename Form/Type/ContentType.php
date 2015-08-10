<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Form\Type\ContentType as BaseContentType;
use Opifer\CmsBundle\Form\DataTransformer\UsernameToUserTransformer;

class ContentType extends BaseContentType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new UsernameToUserTransformer($this->contentManager->getEntityManager());

        $builder
            ->add('indexable', 'checkbox', [
                'attr'  => [
                    'align_with_widget' => true,
                    'class' => 'before-form-section'
                ]
            ])
            ->add('searchable', 'checkbox', [
                'attr'  => [
                    'align_with_widget' => true,
                    'class' => 'before-form-section'
                ]
            ]);
//            ->add(
//                $builder->create('author', 'text', ['label' => 'User', 'attr' => ['class' => 'typeahead_users']])
//                    ->addModelTransformer($transformer)
//            );

        parent::buildForm($builder, $options);
    }
}
