<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\ContentBundle\Controller\Backend\DirectoryController as BaseDirectoryController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DirectoryController extends BaseDirectoryController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $source = new Entity('OpiferCmsBundle:Directory');

        $editAction = new RowAction('edit', 'opifer_content_directory_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('delete', 'opifer_content_directory_delete');
        $deleteAction->setRouteParameters(['id']);

        $grid = $this->get('grid');
        $grid->setId('directories')
            ->setSource($source)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Directory:index.html.twig');
    }
}
