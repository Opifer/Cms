<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Opifer\ContentBundle\Form\Type\PageManagerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Action\DeleteMassAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Opifer\ContentBundle\Entity\Template;

/**
 * Class TemplateController
 *
 * @package Opifer\ContentBundle\Controller\Backend
 */
class TemplateController extends Controller
{

    /**
     * Index
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        // Creates simple grid based on your entity (ORM)
        $source = new Entity('OpiferContentBundle:Template');
        $grid = $this->get('grid');
        $rowAction = new RowAction('link.edit', 'opifer_content_template_index');
        $rowAction->setRouteParameters(['id']);
        $editorAction = new RowAction('link.editor', 'opifer_content_template_editor');
        $editorAction->setRouteParameters(['id']);
        $deleteAction = new RowAction('link.delete', 'opifer.crud.delete', true, '_self', array('class' => 'grid_delete_action text-danger'));
        $deleteAction->setRouteParameters(['slug' => 'templates', 'id'])
            ->setRouteParametersMapping(['id' => 'id'])
            ->manipulateRender(
                function ($action, $row) {
                    $action->setConfirmMessage('Delete template' . $row->getField('name') . '?');

                    return $action;
                }
            );
        $massDeleteAction = new DeleteMassAction();
        $massDeleteAction->setTitle('link.delete');
        $grid->setId('templates')
            ->setSource($source)
            ->setPersistence(true)// remember filters, sort, state etc in session
            ->setDefaultOrder('id', 'desc')
            ->addRowAction($rowAction)
            ->addRowAction($deleteAction)
            ->addRowAction($editorAction)
            ->addMassAction($massDeleteAction)
            ->setVisibleColumns(['id', 'name', 'displayName']);

        $grid->isReadyForRedirect();

        return $grid->getGridResponse('OpiferContentBundle:Template:index.html.twig', ['title' => 'title.templates']);
    }


    /**
     * Graphical Template editor
     *
     * @param Request  $request
     * @param Template $template
     *
     * @return Response
     */
    public function editorAction(Request $request, Template $template)
    {
        $form = $this->createForm(new PageManagerType, $template);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('opifer.content.block_manager')->save($template);
        }

        $parameters = [
            'subject_type' => 'template',
            'subject_id' => $template->getId(),
            'view_url' => $this->generateUrl('opifer_content_template_editor_view', ['id' => $template->getId()]),
            'form' => $form->createView()];

        return $this->render('OpiferContentBundle:PageManager:editor.html.twig', $parameters);
    }

    /**
     * @param Template $template
     *
     * @return Response
     */
    public function editorViewAction(Request $request, Template $template)
    {
        $request->request->set('blockMode', 'manage');
        return $this->render($template->getView(), ['block' => $template]);
    }

}