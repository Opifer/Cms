<?php

namespace Opifer\MediaBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Opifer\MediaBundle\Provider\Pool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Media Picker Form Type
 *
 * Renders a media picker field in a form
 */
class MediaPickerType extends AbstractType
{
    /**
     * @var  \Opifer\MediaBundle\Provider\Pool
     */
    protected $providerPool;

    /**
     * Constructor
     *
     * @param Pool $providerPool
     */
    public function __construct(Pool $providerPool)
    {
        $this->providerPool = $providerPool;
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
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'mediapicker';
    }
}
