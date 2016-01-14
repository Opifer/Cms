<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Opifer\EavBundle\Form\Type\AttributeType as EavAttributeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Opifer\CmsBundle\Entity\Post;

class AttributeType extends EavAttributeType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $attribute = $event->getData();
            $form = $event->getForm();

            if ($attribute && $attribute->getSchema()->getObjectClass() == get_class(new Post())) {
                $form->add('parameters', 'admin_attribute_parameters', [
                    'label' => false,
                    'attr' => [
                        'align_with_widget' => true,
                        'widget_col' => 6,
                    ],
                ]);
            }
        });

        parent::buildForm($builder, $options);
    }
}
