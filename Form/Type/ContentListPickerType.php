<?php

namespace Opifer\ContentBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Multi Content Picker Form Type
 */
class ContentListPickerType extends EntityType
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
    public function getName()
    {
        return 'content_list_picker';
    }
}
