<?php

namespace Opifer\ContentBundle\Event;

use Opifer\ContentBundle\Model\DirectoryInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class DirectoryEvent extends Event
{
    private $request;
    private $directory;

    /**
     * Constructor
     *
     * @param DirectoryInterface $directory
     * @param Request          $request
     */
    public function __construct(DirectoryInterface $directory, Request $request)
    {
        $this->directory = $directory;
        $this->request = $request;
    }

    /**
     * Get directory
     *
     * @return DirectoryInterface
     */
    public function getContent()
    {
        return $this->directory;
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
