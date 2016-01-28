<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\ContentBundle\Form\Type\ContentTypeType;
use Opifer\ContentBundle\Model\ContentTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends Controller
{
    /**
     * Block shared view.
     *
     * The template defaults to `OpiferContentBundle:Block:shared.html.twig`, but can easily be overwritten
     * in the bundle configuration.
     *
     * @return Response
     */
    public function sharedAction()
    {
        $blocks = $this->get('opifer.content.block_manager')->getRepository()
            ->findBy(['shared' => true]);

        return $this->render($this->getParameter('opifer_content.block_shared_view'), [
            'blocks' => $blocks,
        ]);
    }
}