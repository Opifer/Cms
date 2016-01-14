<?php

namespace Opifer\MediaBundle\Event;

use Opifer\MediaBundle\Model\MediaInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class MediaEvent extends Event
{
    private $request;
    private $media;

    public function __construct(MediaInterface $media, Request $request)
    {
        $this->media = $media;
        $this->request = $request;
    }

    public function getMedia()
    {
        return $this->media;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
