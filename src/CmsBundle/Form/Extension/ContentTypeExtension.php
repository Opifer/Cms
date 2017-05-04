<?php

namespace Opifer\CmsBundle\Form\Extension;

use Opifer\ContentBundle\Form\Type\ContentType;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class ContentTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('medias', MediaPickerType::class, [
                'required'      => false,
                'label'         => 'List image',
                'attr'          => ['help_text' => 'help.content_medias'],
                'multiple'      => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ContentType::class;
    }
}
