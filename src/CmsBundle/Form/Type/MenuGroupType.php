<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\CmsBundle\Form\DataTransformer\MenuGroupTransformer;
use Opifer\CmsBundle\Manager\MenuManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MenuGroupType extends AbstractType
{
    /** @var string */
    protected $menuGroupClass;

    /** @var MenuManager */
    protected $menuManager;

    /**
     * Constructor.
     *
     * @param string      $menuGroupClass
     * @param MenuManager $menuManager
     */
    public function __construct($menuGroupClass, MenuManager $menuManager)
    {
        $this->menuGroupClass = $menuGroupClass;
        $this->menuManager = $menuManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new MenuGroupTransformer($this->menuManager, 'menugroup');

        $builder->add('menugroup', 'entity', [
            'class' => $this->menuGroupClass,
            'query_builder' => function ($er) {
                return $er->createQueryBuilder('d');
            },
            'property' => 'indentedName',
            'empty_value' => '/',
            'required' => false,
            'empty_data' => null,
            'label' => false,
        ])->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_menu_group';
    }
}
