<?php

namespace Opifer\ContentBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Opifer\ContentBundle\Model\DirectoryInterface;
use Opifer\ContentBundle\Event\ResponseEvent;
use Opifer\ContentBundle\OpiferContentEvents as Events;

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
        $event = new ResponseEvent($request);
        $this->get('event_dispatcher')->dispatch(Events::DIRECTORY_CONTROLLER_INDEX, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $directoryId = $request->get('directory_id');

        $directories = $this->get('opifer.content.directory_manager')->findChildren($directoryId);

        $parents = $this->get('opifer.content.directory_manager')->findParent($directoryId);

        $data = $this->get('jms_serializer')->serialize([ 'directories' => $directories, 'parents' => $parents ], 'json');

        return new Response($data, 200, [ 'Content-Type' => 'application/json' ]);
    }
}
