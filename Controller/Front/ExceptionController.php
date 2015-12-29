<?php

namespace Opifer\CmsBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController extends Controller
{
    /**
     * 404 error page
     *
     * @return Response
     */
    public function error404Action()
    {
        $contentRepository = $this->getDoctrine()->getRepository('OpiferCmsBundle:Content');
        $content = $contentRepository->findOneBySlug('404');
        if ($content) {
            return $this->forward('OpiferContentBundle:Frontend/Content:view', [
                'content' => $content,
                'statusCode' => 404
            ]);
        }

        $response = new Response();
        $response->setStatusCode(404);

        return $this->render('OpiferCmsBundle:Exception:error404.html.twig', [], $response);
    }
}
