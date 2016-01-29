<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\CmsBundle\Entity\Post;
use Opifer\EavBundle\Form\Type\SchemaType as EavSchemaType;
use Symfony\Component\Form\FormBuilderInterface;

class SchemaType extends EavSchemaType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if (get_class(new Post()) == $builder->getData()->getObjectClass()) {
            $save = $builder->get('save');
            $builder->remove('save');

            $attributes = $builder->get('attributes');
            $builder->remove('attributes');

            $builder->add('postNotify', 'email', [
                'label' => $this->translator->trans('schema.post_notify'),
                'attr'  => [
                    'placeholder' => $this->translator->trans('form.post_notify.placeholder'),
                    'help_text' => $this->translator->trans('form.post_notify.help_text'),
                ]
            ])
                ->add($save->getName(), $save->getType()->getName(), $save->getOptions())
                ->add($attributes->getName(), $attributes->getType()->getName(), $attributes->getOptions())
            ;
        }
    }
}
