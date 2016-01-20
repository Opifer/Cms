<?php

namespace Opifer\CmsBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Opifer\CmsBundle\Entity\Menu;

class MenuManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = $this->em->getRepository('Opifer\CmsBundle\Entity\Menu');
        }

        return $this->repository;
    }

    /**
     * Saves a menu item and re-orders the menu tree
     *
     * @param Menu $menu
     */
    public function save(Menu $menu)
    {
        $this->em->persist($menu);
        $this->em->flush();

        if (false !== $root = $this->getRepository()->findOneBy(['id' => $menu->getRoot()])) {
            $this->getRepository()->reorder($root, 'sort', 'DESC');
        }

        $this->getRepository()->recover();
        $this->em->flush();
    }

    /**
     * Deletes a menu item
     *
     * @param Menu $menu
     */
    public function delete(Menu $menu)
    {
        $this->em->remove($menu);
        $this->em->flush();
    }
}
