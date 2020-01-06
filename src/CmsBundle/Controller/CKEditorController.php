<?php

namespace Opifer\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CKEditorController extends Controller
{
    /**
     * Content browser for CKEditor.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function contentAction(Request $request)
    {
        return $this->render('OpiferCmsBundle:CKEditor:content.html.twig', [
            'funcNum' => $request->get('CKEditorFuncNum'),
            'CKEditor' => $request->get('CKEditor'),
            'type' => $request->get('type'),
        ]);
    }

    /**
     * Image browser for CKEditor.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function mediaAction(Request $request)
    {
        $providers = $this->get('opifer.media.provider.pool')->getProviders();

        return $this->render('OpiferCmsBundle:CKEditor:media.html.twig', [
            'providers' => $providers,
            'props' => [
                'ckeditor' => [
                    'funcNum' => $request->get('CKEditorFuncNum'),
                    'type' => $request->get('type'),
                ]
            ]
        ]);
    }

    /**
     * Styles set for CKEditor.
     *
     * @return Response
     */
    public function stylesAction()
    {
        $response = $this->render('OpiferCmsBundle:CKEditor:styles.js.twig');

        $response->headers->set('Content-Type', 'application/javascript');

        return $response;
    }

    /**
     * Config JS for CKEditor.
     *
     * @return Response
     */
    public function configAction()
    {
        $response = $this->render('OpiferCmsBundle:CKEditor:config.js.twig', [
            'css_path' => $this->getParameter('opifer_cms.ckeditor_css_path'),
        ]);

        $response->headers->set('Content-Type', 'application/javascript');

        return $response;
    }
}
