<?php

namespace Opifer\CmsBundle\ValueProvider;

use Opifer\CmsBundle\Entity\FormValue;
use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class FormValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /** @var string */
    protected $formClass;

    /**
     * @param string $formClass
     */
    public function __construct($formClass)
    {
        $this->formClass = $formClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('form', EntityType::class, [
            'empty_value' => '-- None --',
            'expanded' => false,
            'multiple' => false,
            'class' => $this->formClass,
            'choice_label' => 'name',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return FormValue::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Form';
    }
}
