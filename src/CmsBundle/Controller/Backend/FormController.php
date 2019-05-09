<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\FormBundle\Controller\FormController as BaseFormController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class FormController extends BaseFormController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('FORM_INDEX');

        $source = new Entity($this->get('opifer.form.form_manager')->getClass());

        $postsColumn = new TextColumn(['id' => 'posts', 'title' => 'Posts', 'source' => false, 'filterable' => false, 'sortable' => false, 'safe' => false]);
        $postsColumn->manipulateRenderCell(function ($value, $row, $router) {
            return '<a href="'.$this->generateUrl('opifer_form_post_index', ['formId' => $row->getEntity()->getId()]).'">'.count($row->getEntity()->getPosts()).' posts</a>';
        });

        $editAction = new RowAction('button.edit', 'opifer_form_form_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('button.delete', 'opifer_form_form_delete');
        $deleteAction->setRouteParameters(['id']);

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('forms')
            ->setSource($source)
            ->addColumn($postsColumn)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Form:index.html.twig');
    }
}
