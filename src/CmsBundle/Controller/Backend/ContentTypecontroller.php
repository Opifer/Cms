<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\ContentBundle\Controller\Backend\ContentTypeController as BaseContentTypeController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentTypeController extends BaseContentTypeController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $source = new Entity($this->getParameter('opifer_content.content_type_class'));

        $editAction = new RowAction('button.edit', 'opifer_content_contenttype_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('button.delete', 'opifer_content_contenttype_delete');
        $deleteAction->setRouteParameters(['id']);

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('forms')
            ->setSource($source)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferCmsBundle:Backend/ContentType:index.html.twig');
    }
}
