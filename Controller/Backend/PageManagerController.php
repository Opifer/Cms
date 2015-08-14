<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Opifer\ContentBundle\Block\AbstractBlockService;
use Opifer\ContentBundle\Form\Type\BlockAdapterFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Opifer\ContentBundle\Event\ResponseEvent;
use Opifer\ContentBundle\Event\ContentResponseEvent;
use Opifer\ContentBundle\OpiferContentEvents as Events;
use Opifer\ContentBundle\Entity\Block;

/**
 * Class PageManagerController
 *
 * @package Opifer\ContentBundle\Controller\Backend
 */
class PageManagerController extends Controller
{

    /**
     * @todo: Refactor so that blocks are persisted in the session block tree and not flushed to DB
     *
     * @param Request $request
     * @param Block   $block
     *
     * @return Response
     */
    public function editBlockAction(Request $request, Block $block)
    {
        $manager = $this->get('opifer.content.block_manager');
        $service = $manager->getService($block);

        $form = $this->createForm(new BlockAdapterFormType($service), $block);

        $form->handleRequest($request);

        $updatePreview = false; // signals parent window preview from iframe to update preview
        if ($form->isValid()) {
            $manager->save($block);
            $updatePreview = true;
        }

        return $this->render($service->getEditView(), ['block_service' => $service, 'block' => $block, 'form' => $form->createView(), 'update_preview' => $updatePreview]);
    }
}
