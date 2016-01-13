<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuParametersType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //        $builder->add('link_behaviour', 'choice', [
//            'choices'  => [
//                'ajax' => 'AJAX Load',
//                'blank' => 'Target blank'
//            ],
//            'required' => false,
//            'label_attr' => [
//                'class' => 'col-lg-offset-2'
//            ],
//            'attr' => [
//                'align_with_widget' => true,
//            ],
//        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_menu_parameters';
    }
}
