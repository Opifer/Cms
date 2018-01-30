<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Opifer\CmsBundle\Entity\Domain;
use Opifer\CmsBundle\Entity\Site;
use Opifer\CmsBundle\Form\Type\SiteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $source = new Entity('OpiferCmsBundle:Site');

        $editAction = new RowAction('edit', 'opifer_cms_site_edit');
        $editAction->setRouteParameters(['id']);

        $deleteAction = new RowAction('delete', 'opifer_cms_site_delete');
        $deleteAction->setRouteParameters(['id']);

        $grid = $this->get('grid');
        $grid->setId('sites')
            ->setSource($source)
            ->addRowAction($editAction)
            ->addRowAction($deleteAction);

        return $grid->getGridResponse('OpiferCmsBundle:Backend/Site:index.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $site = new Site();

        $originalDomains = new ArrayCollection();
        foreach ($site->getDomains() as $domain) {
            $originalDomains->add($domain);
        }

        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // Add new domain
            foreach ($form->getData()->getDomains() as $domain) {
                $domain->setSite($site);
            }
            $em->persist($domain);

            $em->persist($site);
            $em->flush();

            return $this->redirectToRoute('opifer_cms_site_edit', ['id' => $site->getId()]);
        }

        return $this->render('OpiferCmsBundle:Backend/Site:create.html.twig', [
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
        $em = $this->getDoctrine()->getManager();
        $site = $em->getRepository(Site::class)->find($id);

        $originalDomains = new ArrayCollection();
        foreach ($site->getDomains() as $domain) {
            $originalDomains->add($domain);
        }

        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // Remove deleted domains
            foreach ($originalDomains as $domain) {
                if (false === $site->getDomains()->contains($domain)) {
                    $em->remove($domain);
                }
            }

            // Add new domain
            foreach ($form->getData()->getDomains() as $domain) {
                $domain->setSite($site);
            }
            $em->persist($domain);

            $em->flush();

            return $this->redirectToRoute('opifer_cms_site_edit', ['id' => $site->getId()]);
        }

        return $this->render('OpiferCmsBundle:Backend/Site:edit.html.twig', [
            'form' => $form->createView(),
            'site' => $site,
        ]);
    }

    /**
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $site = $em->getRepository('OpiferCmsBundle:Site')->find($id);

        $em->remove($site);
        $em->flush();

        return $this->redirectToRoute('opifer_cms_site_index');
    }
}
