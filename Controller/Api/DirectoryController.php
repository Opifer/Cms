<?php

namespace Opifer\ContentBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Opifer\ContentBundle\Model\DirectoryInterface;
use Opifer\ContentBundle\Event\ResponseEvent;
use Opifer\ContentBundle\OpiferContentEvents;

class DirectoryController extends Controller
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
        $dispatcher = $this->get('event_dispatcher');
        $event = new ResponseEvent($request);
        $dispatcher->dispatch(OpiferContentEvents::DIRECTORY_CONTROLLER_INDEX, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $directories = $this->get('opifer.content.directory_manager')->findChildren($request->get('directory_id'));

        $data = $this->get('jms_serializer')->serialize($directories, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }
}
