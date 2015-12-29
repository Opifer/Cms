<?php

namespace Opifer\CmsBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
    /**
     * Render the home page
     *
     * @return Response
     */
    public function homeAction()
    {
        $content = $this->getDoctrine()->getRepository('OpiferCmsBundle:Content')
            ->findOneBySlug('index');

        if ($content) {
            $presentation = json_decode($content->getRealPresentation(), true);
            $layout = json_encode($presentation);
            $layout = $this->get('jms_serializer')->deserialize($layout, 'Opifer\CmsBundle\Entity\Layout', 'json');

            return $this->render($layout->getFilename(), [
                'site'    => $content->getSite(),
                'content' => $content,
                'layout'  => $layout
            ]);
        }

        return $this->render('OpiferCmsBundle:Layout:page.html.twig');
    }
}
