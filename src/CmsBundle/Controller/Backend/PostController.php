<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Column\JoinColumn;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\CmsBundle\Entity\Post;
use Opifer\FormBundle\Controller\PostController as BasePostController;
use Symfony\Component\HttpFoundation\Response;

class PostController extends BasePostController
{
    /**
     * index.
     *
     * @param int $formId
     *
     * @return Response
     */
    public function indexAction($formId)
    {
        $this->denyAccessUnlessGranted('POST_INDEX');

        $form = $this->get('opifer.form.form_manager')->getRepository()->find($formId);

        if (!$form) {
            return $this->createNotFoundException();
        }

        $source = new Entity(Post::class);
        $tableAlias = $source->getTableAlias();
        $source->manipulateQuery(function ($query) use ($tableAlias, $form) {
            $query->andWhere($tableAlias.'.form = :form')->setParameter('form', $form);
        });

        $notificationAction = new RowAction('re-send', 'opifer_form_post_notification');
        $notificationAction->setRouteParameters(['id']);

        $viewAction = new RowAction('view', 'opifer_form_post_view');
        $viewAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('delete', 'opifer_form_post_delete');
        $deleteAction->setRouteParameters(['id']);

        $grid = $this->get('grid');
        $grid->setId('posts')
            ->setSource($source)
            ->setDefaultOrder('id', 'desc')
            ->addRowAction($viewAction)
            ->addRowAction($notificationAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Post:index.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Lists all posts from every form
     *
     * @return Response
     */
    public function listAction()
    {
        $this->denyAccessUnlessGranted('POST_LIST');

        $source = new Entity($this->get('opifer.form.post_manager')->getClass());

        $formColumn = new TextColumn(['id' => 'posts', 'title' => 'Form', 'source' => false, 'filterable' => false, 'sortable' => false, 'safe' => false]);
        $formColumn->manipulateRenderCell(function ($value, $row, $router) {
            return '<a href="'.$this->generateUrl('opifer_form_form_edit', ['id'=> $row->getEntity()->getForm()->getId()]).'">'.$row->getEntity()->getForm()->getName().'</a>';
        });

        $viewAction = new RowAction('view', 'opifer_form_post_view');
        $viewAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('delete', 'opifer_form_post_delete');
        $deleteAction->setRouteParameters(['id']);

        $grid = $this->get('grid');
        $grid->setId('posts')
            ->setSource($source)
            ->setDefaultOrder('id', 'desc')
            ->addColumn($formColumn, 2)
            ->addRowAction($viewAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Post:list.html.twig');
    }
}
