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
            'funcNum' => $request->get('CKEditorFuncNum'),
            'CKEditor' => $request->get('CKEditor'),
            'type' => $request->get('type'),
        ]);
    }
}
