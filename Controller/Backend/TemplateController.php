<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\CmsBundle\Entity\Template;
use Opifer\EavBundle\Form\Type\TemplateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $source = new Entity('OpiferCmsBundle:Template');

        $editAction = new RowAction('edit', 'opifer_cms_template_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('delete', 'opifer_cms_template_delete');
        $deleteAction->setRouteParameters(['id']);

        $grid = $this->get('grid');
        $grid->setId('templates')
            ->setSource($source)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Template:index.html.twig');
    }

    /**
     * @param  Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $template = new Template();

        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($template);
            $em->flush();

            return $this->redirectToRoute('opifer_cms_template_edit', ['id' => $template->getId()]);
        }

        return $this->render('OpiferCmsBundle:Backend/Template:create.html.twig', [
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
        $template = $em->getRepository('OpiferCmsBundle:Template')->find($id);

        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('opifer_cms_template_edit', ['id' => $template->getId()]);
        }

        return $this->render('OpiferCmsBundle:Backend/Template:edit.html.twig', [
            'form'   => $form->createView(),
            'template' => $template
        ]);
    }

    /**
     * @param  int $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $template = $em->getRepository('OpiferCmsBundle:Template')->find($id);

        $em->remove($template);
        $em->flush();

        return $this->redirectToRoute('opifer_cms_template_index');
    }
}
