<?php

namespace Opifer\ContentBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Event\ContentResponseEvent;
use Opifer\ContentBundle\Event\ResponseEvent;
use Opifer\ContentBundle\OpiferContentEvents as Events;

use JMS\Serializer\SerializationContext;

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
        $event = new ResponseEvent($request);
        $this->get('event_dispatcher')->dispatch(Events::CONTENT_CONTROLLER_INDEX, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $paginator = $this->get('opifer.content.content_manager')
            ->getPaginatedByRequest($request);

        $contents = $paginator->getCurrentPageResults();

        $contents = $this->get('jms_serializer')->serialize(iterator_to_array($contents), 'json', SerializationContext::create()->setGroups(['list'])->enableMaxDepthChecks());

        $data = [
            'results'       => json_decode($contents, true),
            'total_results' => $paginator->getNbResults()
        ];

        return new JsonResponse($data);
    }

    /**
     * Get a content items by a list of ids
     *
     * @param string $ids
     *
     * @return JsonResponse
     */
    public function idsAction($ids)
    {
        $items = $this->get('opifer.content.content_manager')
            ->getRepository()
            ->findAddressableByIds($ids);

        $contents = $this->get('jms_serializer')->serialize($items, 'json', SerializationContext::create()->setGroups(['list'])->enableMaxDepthChecks());

        $data = [
            'results'       => json_decode($contents, true),
            'total_results' => count($items)
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

        $event = new ContentResponseEvent($content, $request);
        $this->get('event_dispatcher')->dispatch(Events::CONTENT_CONTROLLER_VIEW, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $content = $this->get('jms_serializer')->serialize($content, 'json');

        return new Response($content, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Delete
     *
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);

        $event = new ContentResponseEvent($content, $request);
        $this->get('event_dispatcher')->dispatch(Events::CONTENT_CONTROLLER_DELETE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $em = $this->get('doctrine')->getManager();
        $em->remove($content);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
