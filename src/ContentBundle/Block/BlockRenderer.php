<?php

namespace Opifer\ContentBundle\Block;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class BlockRenderer
{
    //fragment.handler

    protected $templating;

    protected $fragmentHandler;

    public function __construct(EngineInterface $templating, FragmentHandler $fragmentHandler)
    {
        $this->templating = $templating;
        $this->fragmentHandler = $fragmentHandler;
    }

    public function render($view, $parameters, $response = null)
    {
        return $this->templating->renderResponse($view, $parameters, $response);
    }
}
