<?php

namespace Opifer\CmsBundle\ValueProvider;

use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;
use Symfony\Component\Form\FormBuilderInterface;

class AttachmentValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', [
            'required' => (isset($options['attribute']->getParameters()['required'])) ? $options['attribute']->getParameters()['required'] : false,
            'label' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Opifer\CmsBundle\Entity\AttachmentValue';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Attachment';
    }
}
