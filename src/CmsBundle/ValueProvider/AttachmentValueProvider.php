<?php

namespace Opifer\CmsBundle\ValueProvider;

use Opifer\CmsBundle\Entity\AttachmentValue;
use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class AttachmentValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, [
            'required' => (isset($options['attribute']->getParameters()['required'])) ? $options['attribute']->getParameters()['required'] : false,
            'label' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return AttachmentValue::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Attachment';
    }
}
