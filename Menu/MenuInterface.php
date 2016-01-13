<?php

namespace Opifer\CmsBundle\Menu;

interface MenuInterface
{
    /**
     * Renders the current menu.
     *
     * @return \Knp\Menu\MenuItem
     */
    public function render($factory, $options);
}
