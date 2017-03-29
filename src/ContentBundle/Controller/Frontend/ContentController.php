<?php

namespace Opifer\ContentBundle\Controller\Frontend;

use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Environment\Environment;
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
        $debug = $this->getParameter('kernel.debug');
        
        if ($content->getLocale()) {
            $request->setLocale($content->getLocale()->getLocale());
        }

        $request->setLocale($content->getLocale()->getLocale());

        $contentDate = $content->getUpdatedAt();
        $templateDate = $content->getTemplate()->getUpdatedAt();

        $date = $contentDate > $templateDate ? $contentDate : $templateDate;

        $response = new Response();
        // Force the Content-Type to be text/html to avoid caching with incorrect Content-Type.
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        $response->setLastModified($date);
        $response->setPublic();

        if (null === $version && false == $debug && $response->isNotModified($request)) {
            // return the 304 Response immediately
            return $response;
        }

        $version = $request->query->get('_version');

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->setObject($content);

        $response->setStatusCode($statusCode);

        if (null !== $version && $this->isGranted('ROLE_ADMIN')) {
            $environment->setDraft(true);
        }

        $environment->load();

        return $this->render($environment->getView(), $environment->getViewParameters(), $response);
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
