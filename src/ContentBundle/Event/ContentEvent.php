<?php

namespace Opifer\ContentBundle\Event;

use Opifer\ContentBundle\Model\ContentInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class ContentEvent extends Event
{
    private $request;
    private $content;

    /**
     * Constructor
     *
     * @param ContentInterface $content
     * @param Request          $request
     */
    public function __construct(ContentInterface $content, Request $request)
    {
        $this->content = $content;
        $this->request = $request;
    }

    /**
     * Get content
     *
     * @return ContentInterface
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
