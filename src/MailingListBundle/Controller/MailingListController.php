<?php

namespace Opifer\MailingListBundle\Controller;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Opifer\MailingListBundle\Entity\MailingList;
use Opifer\MailingListBundle\Form\Type\MailingListType;

class MailingListController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $source = new Entity('OpiferMailingListBundle:MailingList');

        $editAction = new RowAction('button.edit', 'opifer_mailing_list_mailing_list_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('button.delete', 'opifer_mailing_list_mailing_list_delete', true, '_self');
        $deleteAction->setConfirmMessage('Confirm deleting this entry?');
        $deleteAction->setRouteParameters(['id']);

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('property')
            ->setSource($source)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferMailingListBundle:MailingList:index.html.twig');
    }

    /**
     * Add new Mailing List.
     */
    public function createAction(Request $request)
    {
        $mailingList = new MailingList();

        $form = $this->createForm(new MailingListType(), $mailingList, [
            'action' => $this->generateUrl('opifer_mailing_list_mailing_list_create'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mailingList);
            $em->flush();

            return $this->redirectToRoute('opifer_mailing_list_mailing_list_index');
        }

        return $this->render('OpiferMailingListBundle:MailingList:add.html.twig', [
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
        $mailingList = $this->getDoctrine()->getRepository('OpiferMailingListBundle:MailingList')->find($id);

        $form = $this->createForm(new MailingListType(), $mailingList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('opifer_mailing_list_mailing_list_index');
        }

        return $this->render('OpiferMailingListBundle:MailingList:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete Mailing List.
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        $mailingList = $this->getDoctrine()->getRepository('OpiferMailingListBundle:MailingList')->find($id);

        if (!empty($mailingList)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mailingList);
            $em->flush();
        }

        return $this->redirectToRoute('opifer_mailing_list_mailing_list_index');
    }
}
