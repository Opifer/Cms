<?php

namespace Opifer\CmsBundle\ValueProvider;

use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;

class HtmlValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'ckeditor', [
            'label' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Opifer\CmsBundle\Entity\HtmlValue';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'HTML';
    }
}
