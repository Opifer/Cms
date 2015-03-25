<?php

namespace Opifer\MediaBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Opifer\MediaBundle\EventListener\MediaResponseEvent;
use Opifer\MediaBundle\EventListener\ResponseEvent;
use Opifer\MediaBundle\OpiferMediaEvents;

class MediaController extends Controller
{
    /**
     * Index
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        $dispatcher = $this->get('event_dispatcher');
        $event = new ResponseEvent($request);
        $dispatcher->dispatch(OpiferMediaEvents::MEDIA_CONTROLLER_INDEX, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $media = $this->get('opifer.media.media_manager')->getPaginatedByRequest($request);

        $items = $this->get('jms_serializer')->serialize(iterator_to_array($media->getCurrentPageResults()), 'json');

        return new JsonResponse([
            'results'          => json_decode($items, true),
            'total_results'    => $media->getNbResults(),
            'results_per_page' => $media->getMaxPerPage()
        ]);
    }

    /**
     * Upload
     *
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        $media = $this->get('opifer.media.media_manager')->createMedia();
        $em = $this->getDoctrine()->getManager();

        foreach ($request->files->all() as $files) {
            if ((is_array($files)) || ($files instanceof \Traversable)) {
                foreach ($files as $file) {
                    $media = clone $media;
                    $media->setFile($file);
                    $media->setProvider('image');

                    $em->persist($media);
                }
            } else {
                $media->setFile($files);
                $media->setProvider('image');

                $em->persist($media);
            }
        }
        $em->flush();

        $media = $this->get('jms_serializer')->serialize($media, 'json');

        return new Response($media, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Delete
     *
     * @param  Request $request
     * @param  integer $id
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $id)
    {
        try {
            $mediaManager = $this->get('opifer.media.media_manager');
            $media = $mediaManager->getRepository()->find($id);

            $dispatcher = $this->get('event_dispatcher');
            $event = new MediaResponseEvent($media, $request);
            $dispatcher->dispatch(OpiferMediaEvents::MEDIA_CONTROLLER_DELETE, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $mediaManager->remove($media);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        return new JsonResponse(['success' => true]);
    }
}
