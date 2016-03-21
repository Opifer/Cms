<?php

namespace Opifer\MailingListBundle\Controller;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MailingListController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $source = new Entity('OpiferMailingListBundle:MailingList');

        $editAction = new RowAction('button.view', 'opifer_mailing_list_mailing_list_view');
        $editAction->setRouteParameters(['id']);

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('property')
            ->setSource($source)
            ->addRowAction($editAction);

        return $grid->getGridResponse('OpiferMailingListBundle:MailingList:index.html.twig');
    }

}
