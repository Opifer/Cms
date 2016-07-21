<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class BlockRenderer
{
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

    public function renderEsi(BlockInterface $block, $query = [])
    {
        $reference = new ControllerReference('OpiferContentBundle:Frontend/Block:view', ['id' => $block->getId()], $query);

        return $this->fragmentHandler->render($reference, 'esi');
    }
}
