<?php

namespace Opifer\CmsBundle\ValueProvider;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;
use Symfony\Component\Form\FormBuilderInterface;

class HtmlValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', CKEditorType::class, [
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
