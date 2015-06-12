<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AttributeParametersType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('required', 'checkbox', [
            'required' => false,
            'label_attr' => [
                'class' => 'col-lg-offset-2',
            ],
            'attr' => [
                'align_with_widget' => true,
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'admin_attribute_parameters';
    }
}
