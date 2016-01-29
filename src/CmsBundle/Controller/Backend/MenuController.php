<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\CmsBundle\Entity\MenuGroup;
use Opifer\CmsBundle\Entity\MenuItem;
use Opifer\CmsBundle\Form\Type\MenuType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller
{
    /**
     * Menu index
     *
     * @return Response
     */
    public function indexAction()
    {
        $qb = $this->container->get('opifer.cms.menu_manager')->getRepository()
            ->getNodesHierarchyQueryBuilder();
        $source = new Entity('OpiferCmsBundle:Menu');
        $source->initQueryBuilder($qb);

        $editAction = new RowAction('edit', 'opifer_cms_menu_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('delete', 'opifer_cms_menu_delete');
        $deleteAction->setRouteParameters(['id']);

        $grid = $this->get('grid');
        $grid->setId('menus')
            ->setSource($source)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction);

        $grid->getColumn('name')
            ->setSafe(false)
            ->setFilterable(false)
            ->setSortable(false)
            ->manipulateRenderCell(
            function($value, $row, $router) {
                $item = $row->getEntity();

                $indentation = ($item->getLvl()) ? str_repeat('&nbsp;', $item->getLvl() * 4) : '';
                if ($item instanceof MenuItem) {
                    $route = $router->generate('opifer_cms_menu_edit', ['id' => $item->getId()]);
                    $html = $indentation . '<a href="'.$route.'"><span class="text-muted">'. $item->getName() .'</span></a>';
                } else {
                    $html = $indentation . '<span class="text-muted">'. $item->getName() .'</span>';
                }

                return $html;
            }
        );

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Menu:index.html.twig');
    }

    /**
     * Create a new menu or menuitem.
     *
     * @param Request $request
     * @param string  $type    group|item
     *
     * @return Response
     */
    public function createAction(Request $request, $type = 'item')
    {
        if ($type == 'group') {
            $menu = new MenuGroup();
        } else {
            $menu = new MenuItem();
        }

        $form = $this->createForm(new MenuType(), $menu);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->container->get('opifer.cms.menu_manager')->save($menu);

            return $this->redirectToRoute('opifer_cms_menu_index');
        }

        return $this->render('OpiferCmsBundle:Backend/Menu:edit.html.twig', [
            'menu' => $menu,
            'localeMenu' => '',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit an existing menu item.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function editAction(Request $request, $id = 0)
    {
        $menuManager = $this->container->get('opifer.cms.menu_manager');

        $menu = $menuManager->getRepository()->findOneById($id);

        if (!$menu) {
            throw $this->createNotFoundException(sprintf('No menu found for id %d', $id));
        }

        $form = $this->createForm(new MenuType(), $menu);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $menuManager->save($menu);

            return $this->redirectToRoute('opifer_cms_menu_index');
        }

        return $this->render('OpiferCmsBundle:Backend/Menu:edit.html.twig', [
            'menu' => $menu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete an existing menu item.
     *
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $menuManager = $this->container->get('opifer.cms.menu_manager');
        $menu = $menuManager->getRepository()->findOneById($id);

        if (!$menu) {
            throw $this->createNotFoundException(sprintf('No menu found for id %d', $id));
        }

        $menuManager->delete($menu);

        return $this->redirectToRoute('opifer_cms_menu_index');
    }
}
