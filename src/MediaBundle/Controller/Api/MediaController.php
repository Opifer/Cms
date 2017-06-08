<?php

namespace Opifer\MediaBundle\Controller\Api;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Opifer\MediaBundle\Event\MediaResponseEvent;
use Opifer\MediaBundle\Event\ResponseEvent;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\MediaBundle\OpiferMediaEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class MediaController extends Controller
{
    /**
     * Index.
     *
     * @ApiDoc
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

        $directories = [];
        if (!$request->get('ids', null)) {
            $directories = $this->get('opifer.media.media_directory_manager')
                ->getRepository()
                ->findByDirectory($request->get('directory', null));
        }

        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');

        $items = $serializer->serialize(iterator_to_array($media->getCurrentPageResults()), 'json', SerializationContext::create()->setGroups(['Default', 'list']));

        $maxUploadSize = (ini_get('post_max_size') < ini_get('upload_max_filesize')) ? ini_get('post_max_size') : ini_get('upload_max_filesize');
        
        return new JsonResponse([
            'directories' => json_decode($serializer->serialize($directories, 'json', SerializationContext::create()->enableMaxDepthChecks()), true),
            'results' => json_decode($items, true),
            'total_results' => $media->getNbResults(),
            'results_per_page' => $media->getMaxPerPage(),
            'max_upload_size' => $maxUploadSize,
        ]);
    }

    /**
     * Get a single media item
     *
     * @ApiDoc
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
     * Update a media item
     *
     * @ApiDoc
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAction(Request $request, $id)
    {
        $content = json_decode($request->getContent(), true);

        /** @var MediaInterface $media */
        $media = $this->get('opifer.media.media_manager')->getRepository()->find($id);

        if (isset($content['name'])) $media->setName($content['name']);
        if (isset($content['alt'])) $media->setAlt($content['alt']);

        if (isset($content['directory'])) {
            $directory = $this->get('opifer.media.media_directory_manager')->getRepository()->find($content['directory']);
            if ($directory) {
                $media->setDirectory($directory);
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $media = $this->get('jms_serializer')->serialize($media, 'json', SerializationContext::create()->setGroups(['Default', 'detail']));

        return new JsonResponse(json_decode($media, true));
    }

    /**
     * Upload.
     *
     * @ApiDoc
     *
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $directory = null;
        if ($request->get('directory')) {
            $directory = $this->get('opifer.media.media_directory_manager')->getRepository()
                ->find($request->get('directory'));
        }

        $uploads = [];
        foreach ($request->files->all() as $files) {
            if ((!is_array($files)) && (!$files instanceof \Traversable)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                $media = $this->get('opifer.media.media_manager')->createMedia();
                $media->setFile($file);
                if ($directory) {
                    $media->setDirectory($directory);
                }

                if (strpos($file->getClientMimeType(), 'image') !== false) {
                    $media->setProvider('image');
                } else {
                    $media->setProvider('file');
                }

                $em->persist($media);
                $uploads[] = $media;
            }
        }
        $em->flush();

        $media = $this->get('jms_serializer')->serialize($uploads, 'json');

        return new JsonResponse(json_decode($media, true));
    }

    /**
     * Delete.
     *
     * @ApiDoc
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
