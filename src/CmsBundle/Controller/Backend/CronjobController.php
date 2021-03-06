<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\CmsBundle\Entity\Cron;
use Opifer\CmsBundle\Form\Type\CronjobType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CronjobController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('CRONJOB_INDEX');

        $source = new Entity('OpiferCmsBundle:Cron');

        $editAction = new RowAction('edit', 'opifer_cms_cronjob_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('delete', 'opifer_cms_cronjob_delete');
        $deleteAction->setRouteParameters(['id']);

        $resetAction = new RowAction('reset', 'opifer_cms_cronjob_reset', true, '_self');
        $resetAction->setConfirmMessage('Confirm reset?');
        $resetAction->setRouteParameters(['id']);

        $grid = $this->get('grid');
        $grid->setId('cronjobs')
            ->setSource($source)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction)
            ->addRowAction($resetAction);

        $grid->getColumn('state')->manipulateRenderCell(function ($value, $row, $router) {
            switch ($value) {
                case Cron::STATE_RUNNING:
                    $label = 'info';
                    break;
                case Cron::STATE_FINISHED :
                    $label = 'success';
                    break;
                case Cron::STATE_TERMINATED :
                case Cron::STATE_CANCELED :
                case Cron::STATE_FAILED :
                    $label = 'danger';
                    break;
                default :
                    $label = 'default';
                    break;
            }

            return '<span class="label label-'.$label.'">'.$value.'</span>';
        });

        $grid->getColumn('state')->setSafe(false);

        return $grid->getGridResponse('@OpiferCms/Backend/Cronjob/index.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('CRONJOB_CREATE');

        $cron = new Cron();

        $form = $this->createForm(CronjobType::class, $cron);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cron);
            $em->flush();

            return $this->redirectToRoute('opifer_cms_cronjob_edit', ['id' => $cron->getId()]);
        }

        return $this->render('@OpiferCms/Backend/Cronjob/create.html.twig', [
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
        $this->denyAccessUnlessGranted('CRONJOB_EDIT');

        $em = $this->getDoctrine()->getManager();
        $cron = $em->getRepository(Cron::class)->find($id);

        $form = $this->createForm(CronjobType::class, $cron);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('opifer_cms_cronjob_edit', ['id' => $cron->getId()]);
        }

        return $this->render('@OpiferCms/Backend/Cronjob/edit.html.twig', [
            'form' => $form->createView(),
            'cron' => $cron,
        ]);
    }

    /**
     * @todo
     *
     * @param int $id
     */
    public function activateAction($id)
    {
        // Nothing yet
    }

    /**
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('CRONJOB_DELETE');

        $em = $this->getDoctrine()->getManager();
        $cron = $em->getRepository('OpiferCmsBundle:Cron')->find($id);

        $em->remove($cron);
        $em->flush();

        return $this->redirectToRoute('opifer_cms_cronjob_index');
    }

    /**
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function resetAction($id)
    {
        $this->denyAccessUnlessGranted('CRONJOB_RESET');

        $em = $this->getDoctrine()->getManager();
        $cron = $em->getRepository('OpiferCmsBundle:Cron')->find($id);

        $cron->setState(Cron::STATE_FINISHED);
        $cron->setLastError('');

        $em = $this->getDoctrine()->getManager();
        $em->persist($cron);
        $em->flush();

        return $this->redirectToRoute('opifer_cms_cronjob_index');
    }
}
