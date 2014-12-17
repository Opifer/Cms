<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;

class TwitterValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'text', [
//            'label' => false,
            'label' => 'Twitter username',
            'attr'  => [
                'placeholder' => 'Twitter username',
            ]
        ]);
        
//        $builder->add('count', 'text', [
////            'label' => false,
//            'label' => 'Number of tweets to fetch',
//            'attr'  => [
//                'placeholder' => 'Number of tweets to fetch',
//            ]
//            //'attr'  => $attr
//        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\TwitterValue';
    }
    
    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Twitter';
    }
}
