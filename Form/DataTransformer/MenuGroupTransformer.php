<?php

namespace Opifer\CmsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Opifer\CmsBundle\Manager\MenuManager;

/**
 * Class MenuGroupTransformer.
 */
class MenuGroupTransformer implements DataTransformerInterface
{
    /** @var MenuManager */
    protected $menuManager;

    /** @var string */
    protected $key;

    /**
     * Constructor.
     *
     * @param MenuManager $menuManager
     * @param string      $key
     */
    public function __construct(MenuManager $menuManager, $key)
    {
        $this->menuManager = $menuManager;
        $this->key = $key;
    }

    /**
     * Transforms an id to menuGroup.
     *
     * @param string|null $id
     *
     * @return mixed
     */
    public function transform($id)
    {
        if (is_null($id)) {
            return;
        }

        $menuGroup = $this->menuManager->getRepository()->findById($id);

        if ($menuGroup) {
            return [$this->key => $menuGroup[0]];
        }

        return;
    }

    /**
     * Transforms an menuGroup to id.
     *
     * @param array|null $menuGroup
     *
     * @return mixed
     */
    public function reverseTransform($menuGroup)
    {
        if (null == $menuGroup) {
            return;
        }

        if (isset($menuGroup[$this->key]) && $menuGroup[$this->key]) {
            return $menuGroup[$this->key]->getId();
        }

        return;
    }
}
