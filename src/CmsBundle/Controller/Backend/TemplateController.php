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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TemplateController extends Controller
{
    /**
     * @Security("has_role('ROLE_CONTENT_MANAGER')")
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
        $designAction->setRouteParameters(['owner' => 'template', 'id']);
        $designAction->setRouteParametersMapping(['id' => 'ownerId']);

        $grid = $this->get('grid');

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid->setId('templates')
            ->setSource($source)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction)
            ->addRowAction($designAction);

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Template:index.html.twig');
    }

    /**
     * @Security("has_role('ROLE_CONTENT_MANAGER')")
     *
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
     * @Security("has_role('ROLE_CONTENT_MANAGER')")
     *
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
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $template = $em->getRepository('OpiferContentBundle:Template')->find($id);

        if (!$template) {
            return $this->createNotFoundException();
        }

        $em->remove($template);
        $em->flush($template);

        return $this->redirectToRoute('opifer_cms_template_index');
    }
}
