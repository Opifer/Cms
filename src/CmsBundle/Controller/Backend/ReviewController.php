<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\ReviewBundle\Controller\ReviewController as BaseReviewController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ReviewController extends BaseReviewController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        //Check permissions
        $this->denyAccessUnlessGranted('REVIEW_INDEX');

        $source = new Entity($this->get('opifer.review.review_manager')->getClass());

        $editAction = new RowAction('button.edit', 'opifer_review_review_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('button.delete', 'opifer_review_review_delete');
        $deleteAction->setRouteParameters(['id']);

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('reviews')
            ->setSource($source)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Review:index.html.twig');
    }
}
