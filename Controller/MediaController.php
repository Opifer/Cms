<?php

namespace Opifer\MediaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Opifer\MediaBundle\Entity\Media;
use Opifer\MediaBundle\Form\Type\SearchMediaType;
use Opifer\MediaBundle\Form\Type\MediaType;
use Opifer\MediaBundle\Form\Type\DropzoneFieldType;

class MediaController extends Controller
{
    /**
     * @Route(
     *     "/{page}",
     *     name="opifer.media.media.index",
     *     options={"expose"=true},
     *     requirements={"page" = "\d+"}
     * )
     *
     * @param Request $request
     * @param integer $page
     *
     * @return Response
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $mediaitems = $em->getRepository('OpiferMediaBundle:Media')->findActiveWithTags($request->get('q', ''));
        $mediaitems = $this->get('knp_paginator')->paginate($mediaitems, $page, 25);
        
        //$mediaitems = new Pager($qb, $page, $limit);

        $searchform = $this->createForm(new SearchMediaType(), []);

        $providers = $this->get('opifer.media.provider.pool')->getProviders();

        return $this->render('OpiferMediaBundle::index.html.twig', [
            'mediaitems' => $mediaitems,
            'searchform' => $searchform->createView(),
            'providers'  => $providers
        ]);
    }

    /**
     * @Route(
     *     "/new/{provider}",
     *     name="opifer.media.media.new",
     *     options={"expose"=true}
     * )
     *
     * @param Request $request
     * @param string  $provider
     *
     * @return Response
     */
    public function newAction(Request $request, $provider = 'image')
    {
        $media = new Media();
        $mediaProvider = $this->get('opifer.media.provider.pool')->getProvider($provider);

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new MediaType(), $media, ['provider' => $mediaProvider]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($media);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                $media->getName() . ' was succesfully created'
            );

            return new RedirectResponse($this->generateUrl('opifer.media.media.index'));
        }

        return $this->render($mediaProvider->newView(), [
            'form'     => $form->createView(),
            'provider' => $mediaProvider
        ]);
    }

    /**
     * @Route(
     *     "/edit/{id}",
     *     name="opifer.media.media.edit",
     *     options={"expose"=true}
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
        $media = $em->getRepository('OpiferMediaBundle:Media')->find($id);

        $provider = $this->get('opifer.media.provider.pool')->getProvider($media->getProvider());

        // Clone the old Media, so we don't perform any useless actions inside the provider
        $media->old = clone $media;

        $form = $this->createForm(new MediaType(), $media, ['provider' => $provider]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($media);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                $media->getName() . ' was succesfully updated'
            );

            return new RedirectResponse($this->generateUrl('opifer.media.media.index'));
        }

        return $this->render($provider->editView(), [
            'form'  => $form->createView(),
            'media' => $media
        ]);
    }

    /**
     * @Route(
     *     "/update/all",
     *     name="opifer.media.media.updateall"
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @todo  Error handling
     */
    public function updateAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $request->get('mediatype');

        foreach ($form['files'] as $id => $values) {
            $media = $em->getRepository('OpiferMediaBundle:Media')->find($id);

            $form = $this->createForm(new DropzoneFieldType(), $media);
            $form->submit($values);

            if ($form->isValid()) {
                $em->persist($media);
            }
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Yes! The file(s) were added to the media library');

        return new RedirectResponse($this->generateUrl('opifer.media.media.index'));
    }

    /**
     * Deletes an image
     *
     * @Route(
     *     "/delete/{id}",
     *     name="opifer.media.media.delete",
     *     requirements={"id" = "\d+"},
     *     options={"expose"=true}
     * )
     *
     * @param Request $request
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $media = $em->getRepository('OpiferMediaBundle:Media')->find($id);
        $em->remove($media);
        $em->flush();

        return new RedirectResponse($this->generateUrl('opifer.media.media.index'));
    }
}
