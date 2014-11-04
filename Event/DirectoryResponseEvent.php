<?php

namespace Opifer\ContentBundle\Event;

use Symfony\Component\HttpFoundation\Response;

class DirectoryResponseEvent extends DirectoryEvent
{
    private $response;

    /**
     * Set response
     *
     * @param Response $response
     */
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
