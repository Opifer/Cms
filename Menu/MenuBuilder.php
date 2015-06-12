<?php

namespace Opifer\CmsBundle\Menu;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Dynamic menu builder
 *
 * Checking access:
 * $this->security->isGranted('ROLE_SUPER_ADMIN')
 *
 * Get database data:
 * $this->em->getRepository('Opifer:Menu')->findAll()
 */
abstract class MenuBuilder implements ContainerAwareInterface
{
    protected $container;

    /**
     * Set container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Add a divider
     *
     * @param MenuItem $menu  The menu
     * @param string   $child Optionally place the divider on a child
     *
     * @return void
     */
    public function addDivider($menu, $child = null)
    {
        $menu = $child ? $menu[$child] : $menu;

        $menu->addChild('divider', [
            'uri' => null,
            'label' => null,
            'attributes' => ['class' => 'divider']
        ]);
    }

    /**
     * Get icon
     *
     * @param string $name
     *
     * @return string
     */
    protected function icon($name)
    {
        return '<i class="fa fa-'.$name.'"></i> ';
    }
}
