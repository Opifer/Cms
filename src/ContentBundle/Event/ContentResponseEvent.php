<?php

namespace Opifer\ContentBundle\Event;

use Symfony\Component\HttpFoundation\Response;

class ContentResponseEvent extends ContentEvent
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
