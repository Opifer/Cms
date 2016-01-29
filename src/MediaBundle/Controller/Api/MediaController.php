<?php

namespace Opifer\MediaBundle\Controller\Api;

use JMS\Serializer\SerializationContext;
use Opifer\MediaBundle\Event\MediaResponseEvent;
use Opifer\MediaBundle\Event\ResponseEvent;
use Opifer\MediaBundle\OpiferMediaEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    /**
     * Index.
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

        $items = $this->get('jms_serializer')->serialize(iterator_to_array($media->getCurrentPageResults()), 'json', SerializationContext::create()->setGroups(['Default', 'list']));

        return new JsonResponse([
            'results' => json_decode($items, true),
            'total_results' => $media->getNbResults(),
            'results_per_page' => $media->getMaxPerPage(),
        ]);
    }
    /**
     * Detail.
     *
     * @return JsonResponse
     */
    public function detailAction($id = null)
    {
        $media = $this->get('opifer.media.media_manager')->getRepository()->find($id);

        $media = $this->get('jms_serializer')->serialize($media, 'json', SerializationContext::create()->setGroups(['Default', 'detail']));

        return new JsonResponse(json_decode($media, true));
    }

    /**
     * Upload.
     *
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        $media = $this->get('opifer.media.media_manager')->createMedia();
        $em = $this->getDoctrine()->getManager();

        foreach ($request->files->all() as $files) {
            if ((!is_array($files)) && (!$files instanceof \Traversable)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                $media = clone $media;
                $media->setFile($file);

                if (strpos($file->getClientMimeType(), 'image') !== false) {
                    $media->setProvider('image');
                } else {
                    $media->setProvider('file');
                }

                $em->persist($media);
            }
        }
        $em->flush();

        $media = $this->get('jms_serializer')->serialize($media, 'json');

        return new Response($media, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Delete.
     *
     * @param Request $request
     * @param int     $id
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
