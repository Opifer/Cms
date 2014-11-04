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
     */
    public function indexAction()
    {
        $dispatcher = $this->get('event_dispatcher');
        $event = new ResponseEvent($request);
        $dispatcher->dispatch(OpiferContentEvents::DIRECTORY_INDEX_RESPONSE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $repository = $this->get('opifer.content.directory_manager')->getRepository();
        $repository->verify();
        // can return TRUE if tree is valid, or array of errors found on tree
        $repository->recover();
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $directoryTree = $repository->childrenHierarchy();

        return $this->render('OpiferContentBundle:Directory:index.html.twig', [
            'directoryTree' => $directoryTree
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
        $dispatcher->dispatch(OpiferContentEvents::DIRECTORY_NEW_RESPONSE, $event);
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
        $dispatcher->dispatch(OpiferContentEvents::DIRECTORY_EDIT_RESPONSE, $event);
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
