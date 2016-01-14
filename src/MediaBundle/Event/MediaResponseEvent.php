<?php

namespace Opifer\MediaBundle\Event;

use Symfony\Component\HttpFoundation\Response;

class MediaResponseEvent extends MediaEvent
{
    private $response;

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
