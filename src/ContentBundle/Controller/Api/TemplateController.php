<?php

namespace Opifer\ContentBundle\Controller\Api;

use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Provider\TemplateBlockProvider;
use Opifer\ContentBundle\Serializer\BlockExclusionStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TemplateController extends Controller
{
    /**
     * @ApiDoc()
     *
     * @param int $id
     *
     * @return ContentInterface[]
     */
    public function getTemplateAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $version = $request->query->get('_version');

        /** @var TemplateBlockProvider $templateBlockProvider */
        $templateBlockProvider = $this->get('opifer.content.template_block_provider');
        $template = $templateBlockProvider->getBlockOwner($id);

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->setObject($template);

        if (null !== $version && $this->isGranted('ROLE_ADMIN')) {
            $environment->setDraft(true);
        }

        $environment->load();

        $blocks = $environment->getRootBlocks();

        $context = SerializationContext::create();
        $context->setGroups(['Default', 'tree', 'detail']);

        $contentItem = [
            'id' => $template->getId(),
            'name' => $template->getName(),
            'display_name' => $template->getDisplayName(),
            'blocks' => $blocks,
        ];

        $json = $this->get('jms_serializer')->serialize($contentItem, 'json', $context);

        return new JsonResponse(json_decode($json, true));
    }
}
