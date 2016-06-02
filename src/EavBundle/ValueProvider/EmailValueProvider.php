<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Entity\EmailValue;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

class EmailValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', EmailType::class, [
            'required' => ($options['attribute']->getRequired()) ? true : false,
            'label' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return EmailValue::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Email';
    }
}
