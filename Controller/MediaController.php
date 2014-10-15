<?php

namespace Opifer\MediaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Opifer\MediaBundle\Event\MediaResponseEvent;
use Opifer\MediaBundle\Event\ResponseEvent;
use Opifer\MediaBundle\Form\Type\MediaType;
use Opifer\MediaBundle\Form\Type\DropzoneFieldType;
use Opifer\MediaBundle\OpiferMediaEvents;

class MediaController extends Controller
{
    /**
     * @Route(
     *     "/",
     *     name="opifer.media.media.index",
     *     options={"expose"=true}
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $dispatcher = $this->get('event_dispatcher');
        $event = new ResponseEvent($request);
        $dispatcher->dispatch(OpiferMediaEvents::MEDIA_CONTROLLER_INDEX, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $providers = $this->get('opifer.media.provider.pool')->getProviders();

        return $this->render('OpiferMediaBundle:Base:index.html.twig', [
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
        $dispatcher = $this->get('event_dispatcher');
        $event = new ResponseEvent($request);
        $dispatcher->dispatch(OpiferMediaEvents::MEDIA_CONTROLLER_NEW, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $mediaManager = $this->get('opifer.media.media_manager');
        $mediaProvider = $this->get('opifer.media.provider.pool')->getProvider($provider);

        $media = $mediaManager->createMedia();

        $form = $this->createForm(new MediaType(), $media, ['provider' => $mediaProvider]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $mediaManager->save($media);

            $this->get('session')->getFlashBag()->add('success',
                $media->getName() . ' was succesfully created'
            );

            return $this->redirect($this->generateUrl('opifer.media.media.index'));
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
        $mediaManager = $this->get('opifer.media.media_manager');
        $media = $mediaManager->getRepository()->find($id);

        $dispatcher = $this->get('event_dispatcher');
        $event = new MediaResponseEvent($media, $request);
        $dispatcher->dispatch(OpiferMediaEvents::MEDIA_CONTROLLER_EDIT, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $provider = $this->get('opifer.media.provider.pool')->getProvider($media->getProvider());

        // Clone the old Media, so we don't perform any useless actions inside the provider
        $media->old = clone $media;

        $form = $this->createForm(new MediaType(), $media, ['provider' => $provider]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $mediaManager->save($media);

            $this->get('session')->getFlashBag()->add('success',
                $media->getName() . ' was succesfully updated'
            );

            return $this->redirect($this->generateUrl('opifer.media.media.index'));
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
     * @param  Request $request
     *
     * @return Response
     */
    public function updateAllAction(Request $request)
    {
        $dispatcher = $this->get('event_dispatcher');
        $event = new ResponseEvent($request);
        $dispatcher->dispatch(OpiferMediaEvents::MEDIA_CONTROLLER_UPDATEALL, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $em = $this->getDoctrine()->getManager();
        $form = $request->get('mediatype');

        foreach ($form['files'] as $id => $values) {
            $media = $this->get('opifer.media.media_manager')->getRepository()->find($id);

            $form = $this->createForm(new DropzoneFieldType(), $media);
            $form->submit($values);

            if ($form->isValid()) {
                $em->persist($media);
            }
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'The file(s) were added to the media library');

        return $this->redirect($this->generateUrl('opifer.media.media.index'));
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $mediaManager = $this->get('opifer.media.media_manager');
        $media = $mediaManager->getRepository()->find($id);

        $dispatcher = $this->get('event_dispatcher');
        $event = new MediaResponseEvent($media, $request);
        $dispatcher->dispatch(OpiferMediaEvents::MEDIA_CONTROLLER_DELETE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $mediaManager->remove($media);

        return $this->redirect($this->generateUrl('opifer.media.media.index'));
    }
}
