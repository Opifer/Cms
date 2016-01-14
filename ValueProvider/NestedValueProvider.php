<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;

class NestedValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /** @var string */
    protected $nestedClass;

    /**
     * Constructor
     *
     * @param string $nestedClass
     */
    public function __construct($nestedClass)
    {
        if ($nestedClass != '' && !is_subclass_of($nestedClass, 'Opifer\EavBundle\Model\Nestable')) {
            throw new \Exception($nestedClass.' must implement Opifer\EavBundle\Model\Nestable');
        }

        if ($nestedClass == '') {
            $this->enabled = false;
        }

        $this->nestedClass = $nestedClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // The form is built manually in the eav_value form fragment inside
        // Resources/views/forms/fields.html.twig
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\NestedValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Nested content';
    }
}