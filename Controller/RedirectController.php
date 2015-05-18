<?php

namespace Opifer\RedirectBundle\Controller;

use Opifer\RedirectBundle\Form\Type\RedirectType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectController extends Controller
{
    /**
     * Index
     *
     * @return Response
     */
    public function indexAction()
    {
        $redirects = $this->get('opifer.redirect.redirect_manager')->getRepository()->findAll();

        return $this->render('OpiferRedirectBundle:Redirect:index.html.twig', [
            'redirects' => $redirects
        ]);
    }

    /**
     * New
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $manager = $this->get('opifer.redirect.redirect_manager');

        $redirect = $manager->createNew();

        $form = $this->createForm(new RedirectType(), $redirect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($redirect);

            return $this->redirectToRoute('opifer_redirect_edit', ['id' => $redirect->getId()]);
        }

        return $this->render('OpiferRedirectBundle:Redirect:create.html.twig');
    }

    /**
     * Edit
     *
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $manager = $this->get('opifer.redirect.redirect_manager');

        $redirect = $manager->getRepository()->find($id);

        $form = $this->createForm(new RedirectType(), $redirect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($redirect);

            return $this->redirectToRoute('opifer_redirect_edit', ['id' => $redirect->getId()]);
        }

        return $this->render('OpiferRedirectBundle:Redirect:edit.html.twig');
    }

    /**
     * Delete
     *
     * @param $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $manager = $this->get('opifer.redirect.redirect_manager');
        $redirect = $manager->getRepository()->find($id);

        $manager->remove($redirect);

        return $this->redirectToRoute('opifer_redirect_index');
    }
}
