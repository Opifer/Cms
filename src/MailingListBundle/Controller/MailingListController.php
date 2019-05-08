<?php

namespace Opifer\MailingListBundle\Controller;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Opifer\MailingListBundle\Entity\MailingList;
use Opifer\MailingListBundle\Form\Type\MailingListType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class MailingListController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        //Check permissions
        $this->denyAccessUnlessGranted('MAILINGLIST_INDEX');

        $source = new Entity('OpiferMailingListBundle:MailingList');

        $editAction = new RowAction('button.edit', 'opifer_mailing_list_mailing_list_edit');
        $editAction->setRouteParameters(['id']);

        $subscriptions = new RowAction('button.subscriptions', 'opifer_mailing_list_mailing_list_subscriptions');
        $subscriptions->setRouteParameters(['id']);

        $deleteAction = new RowAction('button.delete', 'opifer_mailing_list_mailing_list_delete', true, '_self');
        $deleteAction->setConfirmMessage('Confirm deleting this entry?');
        $deleteAction->setRouteParameters(['id']);

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('mailinglists')
            ->setSource($source)
            ->addRowAction($subscriptions)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferMailingListBundle:MailingList:index.html.twig');
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return Response
     */
    public function subscriptionsAction(Request $request, $id)
    {
        //Check permissions
        $this->denyAccessUnlessGranted('MAILINGLIST_SUBSCRIPTIONS');

        $list = $this->getDoctrine()->getRepository('OpiferMailingListBundle:MailingList')->find($id);

        $source = new Entity('OpiferMailingListBundle:Subscription');
        $tableAlias = $source->getTableAlias();

        $source->manipulateQuery(function ($qb) use ($tableAlias, $id) {
            $qb->innerJoin($tableAlias . '.mailingList', 'm')
                ->andWhere('m.id = :list_id')
                ->setParameter('list_id', $id);
        });

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('subscriptions_'.$id)
            ->setSource($source);

        return $grid->getGridResponse('OpiferMailingListBundle:MailingList:subscriptions.html.twig', ['list' => $list]);
    }

    /**
     * Add new Mailing List.
     */
    public function createAction(Request $request)
    {
        //Check permissions
        $this->denyAccessUnlessGranted('MAILINGLIST_CREATE');
        $mailingList = new MailingList();

        $form = $this->createForm(MailingListType::class, $mailingList, [
            'action' => $this->generateUrl('opifer_mailing_list_mailing_list_create'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mailingList);
            $em->flush();

            return $this->redirectToRoute('opifer_mailing_list_mailing_list_index');
        }

        return $this->render('OpiferMailingListBundle:MailingList:create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit Mailing List.
     *
     * @param Request $request
     * @param int     $id
     */
    public function editAction(Request $request, $id)
    {
        //Check permissions
        $this->denyAccessUnlessGranted('MAILINGLIST_EDIT');

        $mailingList = $this->getDoctrine()->getRepository('OpiferMailingListBundle:MailingList')->find($id);

        $form = $this->createForm(MailingListType::class, $mailingList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('opifer_mailing_list_mailing_list_index');
        }

        return $this->render('OpiferMailingListBundle:MailingList:edit.html.twig', [
            'form' => $form->createView(),
            'mailing_list' => $mailingList,
        ]);
    }

    /**
     * Delete Mailing List.
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        //Check permissions
        $this->denyAccessUnlessGranted('MAILINGLIST_DELETE');

        $mailingList = $this->getDoctrine()->getRepository('OpiferMailingListBundle:MailingList')->find($id);

        if (!empty($mailingList)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mailingList);
            $em->flush();
        }

        return $this->redirectToRoute('opifer_mailing_list_mailing_list_index');
    }
}
