<?php

namespace Opifer\BootstrapBlockBundle\Block\Service;

use Opifer\ContentBundle\Block\Service\SectionBlockService as BaseService;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\FormBuilderInterface;

class SectionBlockService extends BaseService
{

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('default')->add('media', MediaPickerType::class, [
            'required'  => false,
            'multiple' => false,
            'label' => 'label.section_media',
            'attr' => array('label_col' => 12, 'widget_col' => 12),
        ]);
    }

}