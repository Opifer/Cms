<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\ContentBundle\Controller\Backend\ContentController as BaseContentController;
use Opifer\ContentBundle\Designer\AbstractDesignSuite;
use Opifer\ContentBundle\Environment\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends BaseContentController
{
    /**
     * Index view of content by type
     *
     * @param int $type
     * @return Response
     */
    public function typeAction($type)
    {
        $contentType = $this->get('opifer.content.content_type_manager')->getRepository()->find($type);

        if (!$contentType) {
            throw $this->createNotFoundException(sprintf('Content Type with ID %d could not be found.', $type));
        }

        $queryBuilder = $this->get('opifer.content.content_manager')->getRepository()->createValuedQueryBuilder('c');
        $source = new Entity($this->getParameter('opifer_content.content_class'));
        $source->initQueryBuilder($queryBuilder);
        $tableAlias = $source->getTableAlias();
        $source->manipulateQuery(function ($query) use ($tableAlias, $contentType) {
            $query->andWhere($tableAlias . '.contentType = :contentType')->setParameter('contentType', $contentType);
        });

        $editAction = new RowAction('button.edit', 'opifer_content_contenteditor_design');
        $editAction->setRouteParameters(['id', 'owner' => 'content']);
        $editAction->setRouteParametersMapping(['id' => 'ownerId']);

        //$deleteAction = new RowAction('button.delete', 'opifer_content_content_delete');
        //$deleteAction->setRouteParameters(['id']);

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('content')
            ->setSource($source)
            ->addRowAction($editAction);
            //->addRowAction($deleteAction)

        foreach ($contentType->getSchema()->getAttributes() as $attribute) {
            $name = $attribute->getName();
            $column = new TextColumn(['id' => $name, 'field' => 'attributes.'.$attribute->getName().'.value', 'title' => $attribute->getDisplayName()]);
            $column->manipulateRenderCell(
                function($value, $row, $router) use ($name) {
                    $value = $row->getEntity()->getAttributes()[$name];

                    return $value;
                }
            );
            $grid->addColumn($column);
        }

        return $grid->getGridResponse($this->getParameter('opifer_content.content_type_view'), [
            'content_type' => $contentType,
            'grid' => $grid,
        ]);
    }

    /**
     * @param Request $request
     * @param string $type
     * @param int $owner
     * @return Response
     */
    public function historyAction(Request $request, $owner, $id)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draft');

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->load($owner, $id);
        $environment->setBlockMode('manage');

        /** @var AbstractDesignSuite $suite */
        $suite = $this->get(sprintf('opifer.content.%s_design_suite', $owner));
        $suite->load($id);

        return $this->render($this->getParameter('opifer_content.content_history_view'), [
            'environment' => $environment,
            'suite' => $suite,
        ]);
    }
}
