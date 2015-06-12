<?php

namespace Opifer\CmsBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;
use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;

class MenuGroupValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /** @var string */
    protected $menuGroupClass;

    /**
     * Constructor
     *
     * @param string $menuGroupClass
     */
    public function __construct($menuGroupClass)
    {
        $this->menuGroupClass = $menuGroupClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'admin_menu_group');
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\CmsBundle\Entity\MenuGroupValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Menu group';
    }
}
