<?php

namespace Opifer\CmsBundle\Form\Extension;

use Opifer\EavBundle\Form\Type\DateTimePickerType;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Opifer\ReviewBundle\Form\Type\ReviewType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class ReviewTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar', MediaPickerType::class)
            ->add('createdAt', DateTimePickerType::class)
        ;
    }

    public function getExtendedType()
    {
        return ReviewType::class;
    }
}
