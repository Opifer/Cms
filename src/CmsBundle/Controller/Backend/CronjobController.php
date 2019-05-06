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
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @return Response
     */
    public function indexAction()
    {
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

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Cronjob:index.html.twig');
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $cron = new Cron();

        $form = $this->createForm(CronjobType::class, $cron);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cron);
            $em->flush();

            return $this->redirectToRoute('opifer_cms_cronjob_edit', ['id' => $cron->getId()]);
        }

        return $this->render('OpiferCmsBundle:Backend/Cronjob:create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $cron = $em->getRepository(Cron::class)->find($id);

        $form = $this->createForm(CronjobType::class, $cron);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('opifer_cms_cronjob_edit', ['id' => $cron->getId()]);
        }

        return $this->render('OpiferCmsBundle:Backend/Cronjob:edit.html.twig', [
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
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $cron = $em->getRepository('OpiferCmsBundle:Cron')->find($id);

        $em->remove($cron);
        $em->flush();

        return $this->redirectToRoute('opifer_cms_cronjob_index');
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function resetAction($id)
    {
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
