<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Knp\Menu\MenuFactory;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Opifer\ContentBundle\Entity;
use Opifer\ContentBundle\Entity\Directory;
use Opifer\ContentBundle\Event\ResponseEvent;
use Opifer\ContentBundle\Event\DirectoryResponseEvent;
use Opifer\ContentBundle\OpiferContentEvents;

class DirectoryController extends Controller
{
    /**
     * Index action
     *
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $dispatcher = $this->get('event_dispatcher');
        $event = new ResponseEvent($request);
        $dispatcher->dispatch(OpiferContentEvents::DIRECTORY_CONTROLLER_INDEX, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $tree = $this->get('opifer.content.directory_manager')->getTree();
        
        return $this->render('OpiferContentBundle:Directory:index.html.twig', [
            'directoryTree' => $tree
        ]);
    }

    /**
     * New
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $dispatcher = $this->get('event_dispatcher');
        $event = new ResponseEvent($request);
        $dispatcher->dispatch(OpiferContentEvents::DIRECTORY_CONTROLLER_NEW, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $manager = $this->get('opifer.content.directory_manager');

        $directory = $manager->create();

        $form = $this->createForm('opifer_directory', $directory);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->save($directory);

            return $this->redirect($this->generateUrl('opifer_content_directory_index'));
        }

        return $this->render('OpiferContentBundle:Directory:edit.html.twig', [
            'directory' => $directory,
            'form'      => $form->createView()
        ]);
    }

    /**
     * Edit
     *
     * @param  Request $request
     * @param  integer  $id
     *
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $manager = $this->get('opifer.content.directory_manager');

        $directory = $manager->getRepository()->findOneById($id);
        if (!$directory) {
            throw $this->createNotFoundException('No directory found for id ' . $id);
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new DirectoryResponseEvent($directory, $request);
        $dispatcher->dispatch(OpiferContentEvents::DIRECTORY_CONTROLLER_EDIT, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm('opifer_directory', $directory);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->save($directory);

            return $this->redirect($this->generateUrl('opifer_content_directory_index'));
        }

        return $this->render('OpiferContentBundle:Directory:edit.html.twig', [
            'directory' => $directory,
            'form'      => $form->createView()
        ]);
    }
}
