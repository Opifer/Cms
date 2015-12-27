<?php

namespace Opifer\CmsBundle\Menu;

use Opifer\CmsBundle\Entity\Menu;
use Opifer\CmsBundle\Entity\MenuGroup;
use Knp\Menu\MenuItem as KnpMenu;

/**
 * Dynamic menu builder
 *
 * Checking access:
 * $this->security->isGranted('ROLE_SUPER_ADMIN')
 *
 * Get database data:
 * $this->em->getRepository('OpiferCmsBundle:Menu')->findAll()
 */
class SiteMenu extends MenuBuilder implements MenuInterface
{
    /**
     * The dynamic sitemenu
     *
     * @return \Knp\Menu\MenuItem
     */
    public function render($factory, $options)
    {
        $menu = $this->container->get('knp_menu.factory')->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav');

        $menu_name = isset($options['menu_name']) ? $options['menu_name'] : false;
        $menu_id = isset($options['menu_id']) ? $options['menu_id'] : false;

        // This should be changed to $this->getRepository()->childrenHierarchy(),
        // but at this point the content relation is not passed in the array, so
        // we cannot route menu items to a content item yet.
        if ($menu_id) {
            foreach ($this->getRepository()->getSortedChildrenById($menu_id) as $item) {
                $this->addChild($menu, $item);
            }
        } elseif ($menu_name) {
            foreach ($this->getRepository()->getSortedChildrenByName($menu_name) as $item) {
                $this->addChild($menu, $item);
            }
        } else {
            foreach ($this->getRepository()->getSortedChildren() as $item) {
                if (null === $item->getParent()) {
                    $this->addChild($menu, $item);
                }
            }
        }

        return $menu;
    }

    /**
     * Add a child to the menu
     *
     * @param KnpMenu $menu
     * @param Menu    $item
     */
    protected function addChild(KnpMenu $menu, Menu $item)
    {
        $name = $item->getName().$item->getId();

        $options = array();
        $options['label'] = ucfirst($item->getName());
        $options['extras'] = array('safe_label' => true);
        if ($item->getContent()) {
            $options['extras']['content'] = $item->getContent();
        }
        
        if (!$item instanceof MenuGroup) {
            if ($uri = $item->getLink()) {
                $options['uri'] = $uri;
            } elseif ($content = $item->getContent()) {
                $options['route'] = '_content';

                $slug = $content->getSlug();
                if (substr($slug, -6) == '/index') {
                    $slug = rtrim($slug, "index");
                }

                $options['routeParameters'] = ['slug' => $slug];
            }
        } else {
            $options['uri'] = '';
        }

        $menu->addChild($name, $options);

        if ($item->hasChildren()) {
            $menu[$name]->setChildrenAttribute('class', 'sub-nav');

            foreach ($item->getChildren() as $child) {
                $this->addChild($menu[$name], $child);
            }
        }
    }

    /**
     * Get the menu repository
     *
     * @return \Opifer\CmsBundle\Repository\MenuRepository
     */
    protected function getRepository()
    {
        return $this->container->get('doctrine')->getRepository('OpiferCmsBundle:Menu');
    }
}
