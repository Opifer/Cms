<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\ContentBundle\Form\Type\LayoutType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {

        $queryBuilder = $this->get('opifer.content.content_manager')->getRepository()->createQueryBuilder('c')
            ->select('c', 'vs', 'v', 'a')
            ->leftJoin('c.valueSet', 'vs')
            ->leftJoin('vs.values', 'v')
            ->leftJoin('v.attribute', 'a');

        $source = new Entity($this->getParameter('opifer_content.content_class'));
        $source->initQueryBuilder($queryBuilder);
        $tableAlias = $source->getTableAlias();
        $source->manipulateQuery(function ($query) use ($tableAlias) {
            $query->andWhere($tableAlias . '.layout = :layout')->setParameter('layout', true);
        });

        $editAction = new RowAction('button.edit', 'opifer_cms_layout_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('button.delete', 'opifer_cms_layout_delete');
        $deleteAction->setRouteParameters(['id']);

        $designAction = new RowAction('button.design', 'opifer_content_contenteditor_design');
        $designAction->setRouteParameters(['owner' => 'content', 'id']);
        $designAction->setRouteParametersMapping(['id' => 'ownerId']);

        $grid = $this->get('grid');

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid->setId('layout')
            ->setSource($source)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction)
            ->addRowAction($designAction)
            ->setVisibleColumns(['id', 'title', 'updatedAt']);
        ;

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Layout:index.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request, $type = 0)
    {
        /** @var ContentManager $manager */
        $manager = $this->get('opifer.content.content_manager');

        if ($type) {
            $contentType = $this->get('opifer.content.content_type_manager')->getRepository()->find($type);
            $content = $this->get('opifer.eav.eav_manager')->initializeEntity($contentType->getSchema());
            $content->setContentType($contentType);
        } else {
            $content = $manager->initialize();
        }

        $form = $this->createForm(LayoutType::class, $content);
        $form->handleRequest($request);

        if ($form->isValid()) {
            //setSlug because slugListener will create one based on title
            $content->setSlug(sha1(date('y-m-d h:i:s')));
            $content->setAlias(sha1(date('y-m-d h:i:s')));

            $manager->save($content);

            return $this->redirectToRoute('opifer_content_contenteditor_design', [
                'owner' => 'content',
                'ownerId' => $content->getId(),
            ]);
        }

        return $this->render($this->getParameter('opifer_content.content_new_view'), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        /** @var ContentManager $manager */
        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);
        $content = $manager->createMissingValueSet($content);

        $form = $this->createForm(LayoutType::class, $content);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->save($content);

            return $this->redirectToRoute('opifer_cms_layout_index');
        }

        return $this->render($this->getParameter('opifer_content.content_edit_view'), [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Details action for an inline form in the Content Design.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsAction(Request $request, $id)
    {
        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);
        $content = $manager->createMissingValueSet($content);

        $form = $this->createForm(LayoutType::class, $content);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->save($content);
        }

        return $this->render($this->getParameter('opifer_content.content_details_view'), [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $manager = $this->get('opifer.content.content_manager');
        $layout = $manager->getRepository('OpiferContentBundle:Content')->find($id);

        if (!$layout) {
            return $this->createNotFoundException();
        }

        $manager->remove($layout);

        return $this->redirectToRoute('opifer_cms_layout_index');
    }
}
