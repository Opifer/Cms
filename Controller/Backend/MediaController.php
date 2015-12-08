<?php

namespace Opifer\MediaBundle\Controller\Backend;

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
     * Index
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
     * New
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

            return $this->redirect($this->generateUrl('opifer_media_media_index'));
        }

        return $this->render($mediaProvider->newView(), [
            'form'     => $form->createView(),
            'provider' => $mediaProvider
        ]);
    }

    /**
     * Edit
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

            return $this->redirect($this->generateUrl('opifer_media_media_index'));
        }

        return $this->render($provider->editView(), [
            'form'  => $form->createView(),
            'media' => $media
        ]);
    }

    /**
     * Update all
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
        $form = $request->get('opifer_media_media');

        foreach ($form['files'] as $id => $values) {
            $media = $this->get('opifer.media.media_manager')->getRepository()->find($id);

            $form = $this->createForm('opifer_media_edit', $media);
            $form->submit($values);

            if ($form->isValid()) {
                $em->persist($media);
            }
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'The file(s) were added to the media library');

        return $this->redirect($this->generateUrl('opifer_media_media_index'));
    }

    /**
     * Deletes an image
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

        return $this->redirect($this->generateUrl('opifer_media_media_index'));
    }
}
