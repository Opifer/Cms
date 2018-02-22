<?php

namespace Opifer\CmsBundle\Controller\Frontend;

use Opifer\CmsBundle\Entity\Locale;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController extends Controller
{
    /**
     * 404 error page.
     * 
     * @param Request $request
     * @return Response
     */
    public function error404Action(Request $request)
    {
        $host = $request->getHost();
        $slugParts = explode('/', $request->getPathInfo());
        $locale = $this->getDoctrine()->getRepository(Locale::class)
            ->findOneByLocale($slugParts[1]);

        /** @var ContentRepository $contentRepository */
        $contentRepository = $this->getDoctrine()->getRepository('OpiferCmsBundle:Content');


        $content = $contentRepository->findActiveBySlug('404', $host);

        if(!$content) {
            $content = $contentRepository->findOneBySlug($locale->getLocale().'/404');
        }

        if (!$content) {
            $content = $contentRepository->findOneBySlug('404');
        }

        if ($content) {
            return $this->forward('OpiferContentBundle:Frontend/Content:view', [
                'content' => $content,
                'statusCode' => 404,
            ]);
        }

        $response = new Response();
        $response->setStatusCode(404);

        return $this->render('OpiferCmsBundle:Exception:error404.html.twig', [], $response);
    }
}
