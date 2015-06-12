<?php

namespace Opifer\CmsBundle\Menu;

class MainMenu extends MenuBuilder implements MenuInterface
{
    /**
     * The main cms menu
     *
     * @return \Knp\Menu\MenuItem
     */
    public function render()
    {
        $translator = $this->container->get('translator');

        $menu = $this->container->get('knp_menu.factory')->createItem('root', [
            'childrenAttributes' => ['class' => 'navbar-nav']
        ]);

        $menu->addChild('dashboard', [
            'label' => $this->icon('tablet').' '.$translator->trans('menu.dashboard'),
            'route' => 'opifer.cms.dashboard.view',
            'extras' => ['safe_label' => true]
        ]);

        $sites = $this->container->get('doctrine')->getRepository('OpiferCmsBundle:Site')
            ->findAll();

        foreach ($sites as $site) {
            $sitename = $site->getName();
            $menu->addChild($sitename, [
                'label' => $this->icon('tablet').' '.$sitename.' <b class="caret"></b>',
                'labelAttributes' => [
                    'class' => 'dropdown dropdown-toggle',
                    'data-toggle' => 'dropdown',
                ],
                'childrenAttributes' => ['class' => 'dropdown-menu'],
                'extras' => ['safe_label' => true]
            ]);

            $menu[$sitename]->addChild($this->icon('file-o').' '.$translator->trans('menu.site.content'), [
                'route' => 'opifer_content_content_index',
                'extras' => ['safe_label' => true]
            ]);

            $menu[$sitename]->addChild($this->icon('th-list').' '.$translator->trans('menu.site.menu'), [
                'route' => 'opifer.cms.menu.index',
                'routeParameters' => ['siteId' => $site->getId()],
                'extras' => ['safe_label' => true]
            ]);

            $menu[$sitename]->addChild($this->icon('folder-o').' '.$translator->trans('menu.site.directory'), [
                'route' => 'opifer_content_directory_index',
                'routeParameters' => ['siteId' => $site->getId()],
                'extras' => ['safe_label' => true]
            ]);

            $this->addDivider($menu, $sitename);

            $menu[$sitename]->addChild($this->icon('picture-o').' '.$translator->trans('menu.site.media'), [
                'route' => 'opifer_media_media_index',
                'extras' => ['safe_label' => true]
            ]);
        }

        if (!count($sites)) {
            $menu->addChild($translator->trans('site.new'), [
                'route' => 'opifer.crud.index',
                'routeParameters' => ['slug' => 'sites'],
                'extras' => ['safe_label' => true]
            ]);
        }

        return $menu;
    }
}
