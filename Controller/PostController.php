<?php

namespace Opifer\FormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * index.
     *
     * @param int $formId
     *
     * @return Response
     */
    public function indexAction($formId)
    {
        $form = $this->get('opifer.form.form_manager')->getRepository()->find($formId);

        if (!$form) {
            return $this->createNotFoundException();
        }

        return $this->render($this->getParameter('opifer_form.post_index_view'), [
            'form' => $form,
        ]);
    }

    /**
     * View a form post.
     *
     * @param int $id
     *
     * @return Response
     */
    public function viewAction($id)
    {
        $post = $this->get('opifer.form.post_manager')->getRepository()->find($id);

        if (!$post) {
            return $this->createNotFoundException();
        }

        return $this->render($this->getParameter('opifer_form.post_view_view'), [
            'post' => $post,
        ]);
    }

    /**
     * Delete a post.
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $post = $this->get('opifer.form.post_manager')->getRepository()->find($id);

        if (!$post) {
            return $this->createNotFoundException();
        }
        
        $form = $post->getForm();

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('opifer_form_post_index', ['formId' => $form->getId()]);
    }
}
