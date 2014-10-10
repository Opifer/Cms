<?php

namespace Opifer\MediaBundle\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Opifer\MediaBundle\Entity\Media;

class MediaController extends Controller
{
    /**
     * @Route(
     *     "/media",
     *     name="opifer.api.media",
     *     options={"expose"=true}
     * )
     * @Method({"GET"})
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        $media = $this->getDoctrine()->getRepository('OpiferMediaBundle:Media')
            ->findPaginatedByRequest($request);

        $items = $this->get('jms_serializer')->serialize(iterator_to_array($media->getCurrentPageResults()), 'json');

        return new JsonResponse([
            'results'          => json_decode($items, true),
            'total_results'    => $media->getTotalResults(),
            'results_per_page' => $media->getMaxPerPage()
        ]);
    }

    /**
     * @Route(
     *     "/media/upload",
     *     name="opifer.api.media.upload",
     *     options={"expose"=true}
     * )
     * @Method({"POST"})
     *
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        $media = new Media();
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
}
