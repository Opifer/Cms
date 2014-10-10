<?php

namespace Opifer\MediaBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use Opifer\MediaBundle\Provider\Pool as ProviderPool;

/**
 * Media Picker Form Type
 *
 * Renders a media picker field in a form
 */
class MediaPickerType extends EntityType
{
    /**
     * @var  Opifer\MediaBundle\Providers\Pool
     */
    protected $providerPool;

    /**
     * Constructor
     *
     * @param ProviderPool              $providerPool
     * @param ManagerRegistry           $registry
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(ProviderPool $providerPool, ManagerRegistry $registry, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->providerPool = $providerPool;
        
        parent::__construct($registry, $propertyAccessor);
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'providers' => $this->providerPool->getProviders()
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'mediapicker';
    }
}
