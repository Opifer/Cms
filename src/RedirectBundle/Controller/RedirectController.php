<?php

namespace Opifer\RedirectBundle\Controller;

use Opifer\RedirectBundle\Form\Type\RedirectType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class RedirectController extends Controller
{
    /**
     * Index
     *
     * @return Response
     */
    public function indexAction()
    {
        //Check permissions
        $this->denyAccessUnlessGranted('REDIRECT_INDEX');

        $redirects = $this->get('opifer.redirect.redirect_manager')->getRepository()->findAll();

        return $this->render($this->container->getParameter('opifer_redirect.redirect_index_view'), [
            'redirects' => $redirects
        ]);
    }

    /**
     * Create redirect
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        //Check permissions
        $this->denyAccessUnlessGranted('REDIRECT_CREATE');

        $manager = $this->get('opifer.redirect.redirect_manager');

        $redirect = $manager->createNew();

        $form = $this->createForm(RedirectType::class, $redirect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->save($redirect);
            $this->addFlash('success', $this->get('translator')->trans('opifer_redirect.flash.created'));

            if ($form->get('saveAndAdd')->isClicked()){
                return $this->redirectToRoute('opifer_redirect_redirect_create');
            }

            return $this->redirectToRoute('opifer_redirect_redirect_edit', ['id' => $redirect->getId()]);
        }

        return $this->render($this->container->getParameter('opifer_redirect.redirect_create_view'), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit a redirect
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        //Check permissions
        $this->denyAccessUnlessGranted('REDIRECT_EDIT');

        $manager = $this->get('opifer.redirect.redirect_manager');

        $redirect = $manager->getRepository()->find($id);

        $form = $this->createForm(RedirectType::class, $redirect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($redirect);
            $this->addFlash('success', $this->get('translator')->trans('opifer_redirect.flash.updated'));

            return $this->redirectToRoute('opifer_redirect_redirect_edit', ['id' => $redirect->getId()]);
        }

        return $this->render($this->container->getParameter('opifer_redirect.redirect_edit_view'), [
            'form'     => $form->createView(),
            'redirect' => $redirect
        ]);
    }

    /**
     * Delete a redirect
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        //Check permissions
        $this->denyAccessUnlessGranted('REDIRECT_DELETE');

        $manager = $this->get('opifer.redirect.redirect_manager');

        $redirect = $manager->getRepository()->find($id);

        $manager->remove($redirect);
        $this->addFlash('success', $this->get('translator')->trans('opifer_redirect.flash.deleted'));

        return $this->redirectToRoute('opifer_redirect_redirect_index');
    }
}
