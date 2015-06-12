<?php

namespace Opifer\CmsBundle\ValueProvider;

use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;

class HtmlValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'ckeditor', [
            'label' => false
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\CmsBundle\Entity\HtmlValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'HTML';
    }
}
