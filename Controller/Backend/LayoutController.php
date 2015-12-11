<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\CmsBundle\Entity\Layout;
use Opifer\CmsBundle\Form\Type\LayoutType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $source = new Entity('OpiferCmsBundle:Layout');

        $editAction = new RowAction('edit', 'opifer_cms_layout_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('delete', 'opifer_cms_layout_delete');
        $deleteAction->setRouteParameters(['id']);

        $grid = $this->get('grid');
        $grid->setId('layouts')
            ->setSource($source)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Layout:index.html.twig');
    }

    /**
     * @param  Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $layout = new Layout();

        $form = $this->createForm(new LayoutType(), $layout);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($layout);
            $em->flush();

            return $this->redirectToRoute('opifer_cms_layout_edit', ['id' => $layout->getId()]);
        }

        return $this->render('OpiferCmsBundle:Backend/Layout:create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param  Request $request
     * @param  int     $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $layout = $em->getRepository('OpiferCmsBundle:Layout')->find($id);

        $form = $this->createForm(new LayoutType(), $layout);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('opifer_cms_layout_edit', ['id' => $layout->getId()]);
        }

        return $this->render('OpiferCmsBundle:Backend/Layout:edit.html.twig', [
            'form' => $form->createView(),
            'layout' => $layout
        ]);
    }

    /**
     * @param  int $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $layout = $em->getRepository('OpiferCmsBundle:Layout')->find($id);

        $em->remove($layout);
        $em->flush();

        return $this->redirectToRoute('opifer_cms_layout_index');
    }
}
