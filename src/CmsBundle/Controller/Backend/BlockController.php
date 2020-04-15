<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\NumberColumn;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Columns;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\ContentBundle\Controller\Backend\BlockController as BaseBlockController;

class BlockController extends BaseBlockController
{
    /**
     * {@inheritdoc}
     */
    public function sharedAction()
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draft');

        $source = new Entity('OpiferContentBundle:Block');
        $tableAlias = $source->getTableAlias();

        $source->manipulateQuery(
            function ($query) use ($tableAlias) {
                $query
                    ->andWhere("{$tableAlias}.shared = 1")
                    ->andWhere("{$tableAlias}.content IS NULL")
                    ->andWhere("{$tableAlias}.template IS NULL")
                ;
            }
        );

        // TODO: Implement an edit action
        //$editAction = new RowAction('button.edit', 'opifer_content_block_shared_edit');
        //$editAction->setRouteParameters(['id']);

        //$deleteAction = new RowAction('button.delete', 'opifer_content_block_delete');
        //$deleteAction->setRouteParameters(['id']);

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('blocks')
            ->setSource($source)
            ->setVisibleColumns(['id', 'sharedName', 'sharedDisplayName'])
            //->addRowAction($editAction)
            //->addRowAction($deleteAction)
        ;

        return $grid->getGridResponse('@OpiferCms/Backend/Block/shared.html.twig');
    }
}
