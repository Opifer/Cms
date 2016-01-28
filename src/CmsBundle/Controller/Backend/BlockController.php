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
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        $source = new Entity('OpiferContentBundle:Block');
        $tableAlias = $source->getTableAlias();

        $source->manipulateQuery(
            function ($query) use ($tableAlias)
            {
                $query->andWhere("{$tableAlias}.shared = 1")
                        ->andWhere("{$tableAlias}.owner IS NULL");
            }
        );
//
//        $idColumn = new NumberColumn(array('id' => 'id', 'field' => 'id', 'title' => 'label.id'));
//        $nameColumn = new TextColumn(array('id' => 'name', 'field' => 'sharedName', 'title' => 'label.name'));
//        $displayNameColumn = new TextColumn(array('id' => 'display_name', 'field' => 'sharedDisplayName', 'title' => 'label.display_name'));

        $editAction = new RowAction('button.edit', 'opifer_content_block_shared_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('button.delete', 'opifer_content_block_delete');
        $deleteAction->setRouteParameters(['id']);

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('blocks')
            ->setSource($source)
//            ->setColumns(new Columns($this->get('security.context')))
//            ->addColumn($idColumn)
//            ->addColumn($nameColumn)
//            ->addColumn($displayNameColumn)
            ->setVisibleColumns(['id', 'sharedName', 'sharedDisplayName'])
            ->addRowAction($editAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Block:shared.html.twig');
    }
}