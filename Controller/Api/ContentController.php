<?php

namespace Opifer\ContentBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Opifer\ContentBundle\Model\ContentInterface;

class ContentController extends Controller
{
    /**
     * Index
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        $manager = $this->get('opifer.content.content_manager');
        $paginator = $manager->getRepository()->findPaginatedByRequest($request);

        $contents = $paginator->getCurrentPageResults();
        $contents = $this->get('jms_serializer')->serialize(iterator_to_array($contents), 'json');

        $data = [
            'results'       => json_decode($contents, true),
            'total_results' => $paginator->getTotalResults()
        ];

        return new JsonResponse($data);
    }

    /**
     * View
     *
     * @param Request $request
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function viewAction(Request $request, $id)
    {
        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);
        $content = $this->get('jms_serializer')->serialize($content, 'json');

        return new Response($content, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Delete
     *
     * @param integer $id
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }

        $em = $this->get('doctrine')->getManager();

        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);

        $em->remove($content);
        $em->flush();

        return new Response('');
    }
}
