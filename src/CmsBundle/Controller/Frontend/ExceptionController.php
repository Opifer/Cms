<?php

namespace Opifer\CmsBundle\Controller\Frontend;

use Opifer\CmsBundle\Entity\Locale;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController extends Controller
{
    /**
     * 404 error page.
     *
     * @return Response
     */
    public function error404Action()
    {

        $slugParts = explode('/', $this->getRequest()->getPathInfo());
        $localeRepository= $this->getDoctrine()->getRepository(Locale::class);
        $locale = $localeRepository->findOneByLocale($slugParts[1]);

        $contentRepository = $this->getDoctrine()->getRepository('OpiferCmsBundle:Content');

        if($locale) {
            $content = $contentRepository->findOneBySlug($locale->getLocale().'/404');
            if ($content) {
                return $this->forward('OpiferContentBundle:Frontend/Content:view', [
                    'content' => $content,
                    'statusCode' => 404,
                ]);
            }
        }

        $content = $contentRepository->findOneBySlug('404');
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
