<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * DataView Controller
 */
class DataViewController extends Controller
{
    /**
     * Index.
     *
     * Renders the dataview editor as a React app
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('OpiferContentBundle:DataView:index.html.twig');
    }
}
