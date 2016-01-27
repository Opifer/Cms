<?php

namespace Opifer\MediaBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Opifer\MediaBundle\Event\MediaResponseEvent;
use Opifer\MediaBundle\Event\ResponseEvent;
use Opifer\MediaBundle\Form\Type\MediaType;
use Opifer\MediaBundle\OpiferMediaEvents;

class MediaController extends Controller
{
    /**
     * Index.
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

        return $this->render($this->container->getParameter('opifer_media.media_index_view'), [
            'providers' => $providers,
        ]);
    }

    /**
     * Create new media.
     *
     * @param Request $request
     * @param string  $provider
     *
     * @return Response
     */
    public function createAction(Request $request, $provider = 'image')
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

            $this->addFlash('success', sprintf('%s was succesfully created', $media->getName()));

            return $this->redirectToRoute('opifer_media_media_index');
        }

        return $this->render($this->container->getParameter('opifer_media.media_create_view'), [
            'form' => $form->createView(),
            'provider' => $mediaProvider,
        ]);
    }

    /**
     * Edit.
     *
     * @param Request $request
     * @param int     $id
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

        $form = $this->createForm(MediaType::class, $media, ['provider' => $provider]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $mediaManager->save($media);

            $this->addFlash('success', sprintf('%s was succesfully updated', $media->getName()));

            return $this->redirectToRoute('opifer_media_media_index');
        }

        return $this->render($this->container->getParameter('opifer_media.media_edit_view'), [
            'form' => $form->createView(),
            'media' => $media,
        ]);
    }

    /**
     * Update multiple media items.
     *
     * @param Request $request
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

        $this->addFlash('success', 'The file(s) were added to the media library');

        return $this->redirectToRoute('opifer_media_media_index');
    }

    /**
     * Deletes a media item.
     *
     * @param Request $request
     * @param int     $id
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

        return $this->redirectToRoute('opifer_media_media_index');
    }
}
