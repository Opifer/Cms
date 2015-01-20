<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class EavPostType
 */
class EavPostType extends EavType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction($this->router->generate('opifer_eav_form_submit', ['valueId' => $options['valueId']]));
        $builder->add('valueset', 'opifer_valueset');
        $builder->add('save', 'submit');
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'eav_post';
    }
}
