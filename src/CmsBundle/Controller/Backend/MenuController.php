<?php

namespace Opifer\CmsBundle\Controller\Backend;

use Opifer\CmsBundle\Form\Type\MenuType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Opifer\CmsBundle\Entity\MenuGroup;
use Opifer\CmsBundle\Entity\MenuItem;

class MenuController extends Controller
{
    /**
     * Menu index.
     */
    public function indexAction()
    {
        $menuTree = $this->getDoctrine()
            ->getRepository('OpiferCmsBundle:Menu')
            ->childrenHierarchy();

        return $this->render('OpiferCmsBundle:Backend/Menu:index.html.twig', [
            'menu_tree' => $menuTree,
        ]);
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
