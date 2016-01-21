<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\ContentBundle\Entity\Template;
use Opifer\ContentBundle\Form\Type\TemplateType;
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
        $source = new Entity('OpiferContentBundle:Template');

        $editAction = new RowAction('button.edit', 'opifer_cms_template_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('button.delete', 'opifer_cms_template_delete');
        $deleteAction->setRouteParameters(['id']);

        $designAction = new RowAction('button.design', 'opifer_content_contenteditor_design');
        $designAction->setRouteParameters(['type' => 'template', 'id']);

        $grid = $this->get('grid');

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid->setId('templates')
            ->setSource($source)
//            ->addColumn($attributesColumn)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction)
            ->addRowAction($designAction)
            ->setActionsColumnSeparator(' ');

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Template:index.html.twig');
    }

    /**
     * @param Request $request
     *
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
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $template = $em->getRepository('OpiferContentBundle:Template')->find($id);

        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('opifer_cms_template_edit', ['id' => $template->getId()]);
        }

        return $this->render('OpiferCmsBundle:Backend/Template:edit.html.twig', [
            'form' => $form->createView(),
            'template' => $template,
        ]);
    }

    /**
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $template = $this->get('opifer.eav.template_manager')->getRepository()->find($id);

        if (!$template) {
            return $this->createNotFoundException();
        }

        $relatedContent = $em->getRepository('OpiferCmsBundle:Content')
            ->createValuedQueryBuilder('c')
            ->innerJoin('vs.template', 't')
            ->select('COUNT(c)')
            ->where('t.id = :template')
            ->setParameter('template', $id)
            ->getQuery()
            ->getSingleScalarResult();

        if ($relatedContent > 0) {
            $this->addFlash('error', 'template.delete.warning');

            return $this->redirectToRoute('opifer_cms_template_index');
        }

        $em->remove($template);
        $em->flush();

        return $this->redirectToRoute('opifer_cms_template_index');
    }
}
