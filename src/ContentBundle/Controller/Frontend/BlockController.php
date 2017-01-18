<?php

namespace Opifer\ContentBundle\Controller\Frontend;

use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Block Controller
 */
class BlockController extends Controller
{
    /**
     * Renders a single block
     *
     * Useful for Edge Side Includes (ESI)
     *
     * @param $id
     *
     * @return Response
     */
    public function viewAction($id)
    {
        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');

        /** @var BlockInterface $block */
        $block = $manager->getRepository()->find($id);

        if (!$block) {
            throw $this->createNotFoundException();
        }

        $response = new Response();

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->setObject($block->getOwner());

        $service = $manager->getService($block);

        $response = $service->execute($block, $response, [
            'partial' => true,
            'environment' => $environment
        ]);

        return $response;
    }
}
