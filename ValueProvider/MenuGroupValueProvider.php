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
     * Constructor.
     *
     * @param string $menuGroupClass
     */
    public function __construct($menuGroupClass)
    {
        $this->menuGroupClass = $menuGroupClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'admin_menu_group');
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Opifer\CmsBundle\Entity\MenuGroupValue';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Menu group';
    }
}
