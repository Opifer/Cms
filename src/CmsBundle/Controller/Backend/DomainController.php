<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\CmsBundle\Entity\Domain;
use Opifer\CmsBundle\Form\Type\DomainType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DomainController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('DOMAIN_INDEX');

        $source = new Entity(Domain::class);

        $editAction = new RowAction('button.edit', 'opifer_cms_domain_edit');
        $editAction->setRouteParameters(['id']);

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('domain')
            ->setSource($source)
            ->addRowAction($editAction)
        ;

        return $grid->getGridResponse('@OpiferCms/Backend/Domain/index.html.twig');
    }

    /**
     * Create an event
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('DOMAIN_CREATE');

        $domain = new Domain();

        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($domain);
            $em->flush();

            $this->addFlash('success', 'Domaim has been created successfully');

            return $this->redirectToRoute('opifer_cms_domain_edit', ['id' => $domain->getId()]);
        }

        return $this->render('@OpiferCms/Backend/Domain/create.html.twig', [
            'domain' => $domain,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null)
    {
        $this->denyAccessUnlessGranted('DOMAIN_EDIT');

        $em = $this->getDoctrine()->getManager();

        if (is_numeric($id)) {
            $domain = $em->getRepository('OpiferCmsBundle:Domain')->find($id);
        } else {
            $domain = $em->getRepository('OpiferCmsBundle:Domain')->findOneByDomain($id);
        }

        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($domain);
            $em->flush();

            $this->addFlash('success', 'Domain has been updated successfully');

            return $this->redirectToRoute('opifer_cms_domain_edit', ['id' => $domain->getId()]);
        }

        return $this->render('@OpiferCms/Backend/Domain/edit.html.twig', [
            'domain' => $domain,
            'form' => $form->createView()
        ]);
    }
}
