<?php

namespace Opifer\ContentBundle\Controller\Frontend;

use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Environment\ContentEnvironment;
use Opifer\ContentBundle\Model\Content;
use Opifer\ContentBundle\Model\ContentInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Content Controller
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
        $environment = $this->get('opifer.content.block_environment');

        $response->setStatusCode($statusCode);

        if (null !== $version && $this->isGranted('ROLE_ADMIN')) {
            $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');
            $environment->load('content', $content->getId());
        } else {
            $environment->load('content', $content->getId());
        }

        return $this->render($environment->getView(), $environment->getViewParameters());
    }

    /**
     * Render the home page.
     *
     * @return Response
     */
    public function homeAction()
    {
        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->findOneBySlug('index');

        return $this->forward('OpiferContentBundle:Frontend/Content:view', [
            'content' => $content
        ]);
    }
}
