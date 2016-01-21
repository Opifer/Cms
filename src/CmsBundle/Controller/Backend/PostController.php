<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\FormBundle\Controller\PostController as BasePostController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
        $form = $this->get('opifer.form.form_manager')->getRepository()->find($formId);

        if (!$form) {
            return $this->createNotFoundException();
        }

        $source = new Entity('OpiferCmsBundle:Post');
        $tableAlias = $source->getTableAlias();
        $source->manipulateQuery(function ($query) use ($tableAlias, $form) {
            $query->andWhere($tableAlias.'.form = :form')->setParameter('form', $form);
        });

        $viewAction = new RowAction('view', 'opifer_form_post_view');
        $viewAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('delete', 'opifer_form_post_delete');
        $deleteAction->setRouteParameters(['id']);

        $grid = $this->get('grid');
        $grid->setId('posts')
            ->setSource($source)
            ->addRowAction($viewAction)
            ->addRowAction($deleteAction)
            ->setActionsColumnSeparator(' ');

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Post:index.html.twig', ['form' => $form]);
    }
}
