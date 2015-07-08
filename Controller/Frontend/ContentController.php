<?php

namespace Opifer\ContentBundle\Controller\Frontend;

use Opifer\ContentBundle\Model\Content;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\LayoutInterface;

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
        $presentation = json_decode($content->getRealPresentation(), true);
        if (!$presentation) {
            throw new \Exception('The template '.$content->getTemplate()->getName().' does not have a layout attached.');
        }

        $layout = json_encode($presentation);
        $layout = $this->get('jms_serializer')->deserialize($layout, $this->container->getParameter('opifer_content.layout_class'), 'json');

        // If the layout has an action, forward to that action and pass the layout
        // and the content.
        if ($layout->getAction()) {
            return $this->forward($layout->getAction(), [
                'content' => $content,
                'layout'  => $layout
            ]);
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $this->render($layout->getFilename(), [
            'content' => $content,
            'layout'  => $layout
        ], $response);
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
