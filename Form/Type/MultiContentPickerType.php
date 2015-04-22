<?php

namespace Opifer\ContentBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Multi Content Picker Form Type
 */
class MultiContentPickerType extends EntityType
{
    /**
     * Constructor
     *
     * @param ManagerRegistry           $registry
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(ManagerRegistry $registry, PropertyAccessorInterface $propertyAccessor = null)
    {
        parent::__construct($registry, $propertyAccessor);
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        //$view->vars = array_replace($view->vars, [
        //    'providers' => $this->providerPool->getProviders()
        //]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'multicontentpicker';
    }
}
