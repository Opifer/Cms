<?php

namespace Opifer\ContentBundle\Controller\Frontend;

use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Model\Content;
use Opifer\ContentBundle\Model\ContentInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ContentController
 *
 * @package Opifer\ContentBundle\Controller\Frontend
 */
class ContentController extends Controller
{
    /**
     * View a single content page
     *
     * This Controller action is being routed to from either our custom ContentRouter,
     * or the ExceptionController.
     * @see Opifer\SiteBundle\Router\ContentRouter
     *
     * @param Request          $request
     * @param ContentInterface $content
     * @param int              $statusCode
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function viewAction(Request $request, ContentInterface $content, $statusCode = 200)
    {
        $version = $request->query->get('_version');
        $response = new Response();

        /** @var ContentEnvironment $environment */
        $environment = $this->get('opifer.content.block_content_environment');

        $response->setStatusCode($statusCode);

        if (null !== $version && $this->isGranted('ROLE_ADMIN')) {
            $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');
            $environment->load($content->getId(), $version);
        } else {
            $environment->load($content->getId());
        }

        return $this->render($environment->getView(), $environment->getViewParameters());
    }

    /**
     * View a content item by it's ID
     *
     * Simply retrieve the content item and forward it to the default view action
     *
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function nestedAction(Request $request, $content)
    {
        if (!$content instanceof Content) {
            $content = $this->get('opifer.content.content_manager')->getRepository()
                ->findOneById($content);
        }

        // If the content could not be found or is inactive, return an empty response
        // to avoid rendering 404 pages as nested content.
        if (!$content || !$content->getActive()) {
            return new Response('');
        }

        return $this->forward('OpiferContentBundle:Frontend/Content:view', [
            'request' => $request,
            'content' => $content
        ]);
    }
}
