<?php

namespace Opifer\ContentBundle\Controller\Backend;

use GuzzleHttp\Client;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Designer\AbstractDesignSuite;
use Opifer\ContentBundle\Entity\DocumentBlock;
use Opifer\ContentBundle\Environment\ContentEnvironment;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Provider\BlockProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ContentEditorController extends Controller
{
    /**
     * Graphical Content editor
     *
     * @param string  $owner
     * @param integer $ownerId
     *
     * @return Response
     */
    public function designAction($owner, $ownerId)
    {
        /** @var BlockManager $blockManager */
        $blockManager = $this->get('opifer.content.block_manager');
        $manager = $this->get('opifer.content.content_manager');

        /** @var AbstractDesignSuite $suite */
        $suite = $this->get(sprintf('opifer.content.%s_design_suite', $owner));
        $suite->load($ownerId);

        $content = $manager->getRepository()->find($ownerId);
        $this->denyAccessUnlessGranted('CONTENT_DESIGNER', $content);

        $parameters = [
            'manager' => $blockManager,
            'toolset' => $blockManager->getToolset(),
            'id' => $suite->getSubject()->getId(),
            'owner' => $owner,
            'title' => $suite->getTitle(),
            'caption' => $suite->getCaption(),
            'permalink' => $suite->getPermalink(),
            'defaultDomain' => (($content && $content->getSite()) ? $content->getSite()->getDefaultDomain() : null),
            'url_properties' => $suite->getPropertiesUrl(),
            'url_cancel' => $suite->getCancelUrl(),
            'url' => $suite->getCanvasUrl(),
        ];

        return $this->render($this->getParameter('opifer_content.content_design_view'), $parameters);
    }


    /**
     * Table of Contents tree
     *
     * @param string  $owner
     * @param integer $ownerId
     *
     * @return Response
     */
    public function tocAction($owner, $ownerId)
    {
        /** @var BlockProviderInterface $provider */
        $provider = $this->get('opifer.content.block_provider_pool')->getProvider($owner);
        $object = $provider->getBlockOwner($ownerId);

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->setDraft(true)->setObject($object);
        $environment->setBlockMode('manage');

        $twigAnalyzer = $this->get('opifer.content.twig_analyzer');

        $parameters = [
            'environment' => $environment,
            'analyzer' => $twigAnalyzer,
            'object' => $environment->getObject(),
        ];

        return $this->render('OpiferContentBundle:Content:toc.html.twig', $parameters);
    }

    /**
     * @param string  $owner
     * @param integer $ownerId
     *
     * @return mixed
     */
    public function viewAction($owner, $ownerId)
    {
        $frontendUrl = $this->container->getParameter('opifer_content.frontend_url');
        if ($frontendUrl) {
            $client = new Client();
            if ($owner == 'template') {
                $res = $client->request('GET', sprintf('%s/templates/%d?manage=true', $frontendUrl, $ownerId));
            } else {
                $res = $client->request('GET', sprintf('%s/%d?manage=true', $frontendUrl, $ownerId));
            }

            return new Response($res->getBody()->getContents());
        }

        /** @var BlockProviderInterface $provider */
        $provider = $this->get('opifer.content.block_provider_pool')->getProvider($owner);
        $object = $provider->getBlockOwner($ownerId);

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->setDraft(true)->setObject($object);
        $environment->setBlockMode('manage');

        return $this->render($environment->getView(), $environment->getViewParameters());
    }
}
