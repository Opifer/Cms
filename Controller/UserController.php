<?php

namespace Opifer\CmsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * Overriding the CRUD new action
     *
     * @Route(
     *     "/users/new",
     *     name="opifer.cms.user.new"
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $user = $this->container->getParameter('opifer_cms.model.user.class');
        $user = new $user();

        $form = $this->createForm($this->get('opifer.cms.user_form'), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                $this->get('translator')->trans('user.new.success', [
                    '%username%' => ucfirst($user->getUsername()),
                    '%id%'       => $user->getId()
                ])
            );

            return $this->redirect($this->generateUrl('opifer.crud.index', ['slug' => 'users']));
        }

        return $this->render('OpiferCrudBundle:Crud:edit.html.twig', [
            'form'   => $form->createView(),
            'slug'   => 'users',
            'entity' => $user
        ]);
    }

    /**
     * Edit the user
     *
     * @Route(
     *     "/users/edit/{id}",
     *     name="opifer.cms.user.edit",
     *     requirements={"id" = "\d+"}
     * )
     *
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('OpiferCmsBundle:User')->find($id);

        $form = $this->createForm($this->get('opifer.cms.user_form'), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                $this->get('translator')->trans('user.edit.success', [
                    '%username%' => ucfirst($user->getUsername())
                ])
            );

            return $this->redirect($this->generateUrl('opifer.crud.index', ['slug' => 'users']));
        }

        return $this->render('OpiferCrudBundle:Crud:edit.html.twig', [
            'form'   => $form->createView(),
            'slug'   => 'users',
            'entity' => $user
        ]);
    }
}
