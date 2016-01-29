<?php

namespace Opifer\FormBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\FormBundle\Event\Events;
use Opifer\FormBundle\Event\FormSubmitEvent;
use Opifer\FormBundle\Form\Type\FormType;
use Opifer\FormBundle\Form\Type\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FormController extends Controller
{
    /**
     * Form index view.
     *
     * The template defaults to `OpiferFormBundle:Form:index.html.twig`, but can easily be overwritten
     * in the bundle configuration.
     *
     * @return Response
     */
    public function indexAction()
    {
        $forms = $this->get('opifer.form.form_manager')->getRepository()
            ->findAllWithPosts();

        return $this->render($this->getParameter('opifer_form.form_index_view'), [
            'forms' => $forms,
        ]);
    }

    /**
     * Create a form.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $formManager = $this->get('opifer.form.form_manager');

        $form = $formManager->create();

        $formType = $this->createForm(FormType::class, $form);
        $formType->handleRequest($request);

        if ($formType->isSubmitted() && $formType->isValid()) {
            foreach ($formType->getData()->getSchema()->getAttributes() as $attribute) {
                $attribute->setSchema($form->getSchema());

                foreach ($attribute->getOptions() as $option) {
                    $option->setAttribute($attribute);
                }
            }

            $formManager->save($form);

            $this->addFlash('success', 'Form has been created successfully');

            return $this->redirectToRoute('opifer_form_form_edit', ['id' => $form->getId()]);
        }

        return $this->render($this->getParameter('opifer_form.form_create_view'), [
            'form' => $form,
            'form_form' => $formType->createView(),
        ]);
    }

    /**
     * Edit a form.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $formManager = $this->get('opifer.form.form_manager');
        $em = $this->get('doctrine.orm.entity_manager');

        $form = $formManager->getRepository()->find($id);

        if (!$form) {
            return $this->createNotFoundException();
        }

        $originalAttributes = new ArrayCollection();
        foreach ($form->getSchema()->getAttributes() as $attributes) {
            $originalAttributes->add($attributes);
        }

        $formType = $this->createForm(FormType::class, $form);
        $formType->handleRequest($request);

        if ($formType->isSubmitted() && $formType->isValid()) {
            // Remove deleted attributes
            foreach ($originalAttributes as $attribute) {
                if (false === $form->getSchema()->getAttributes()->contains($attribute)) {
                    $em->remove($attribute);
                }
            }

            // Add new attributes
            foreach ($formType->getData()->getSchema()->getAttributes() as $attribute) {
                $attribute->setSchema($form->getSchema());

                foreach ($attribute->getOptions() as $option) {
                    $option->setAttribute($attribute);
                }
            }

            $formManager->save($form);

            $this->addFlash('success', 'Event has been updated successfully');

            return $this->redirectToRoute('opifer_form_form_edit', ['id' => $form->getId()]);
        }

        return $this->render($this->getParameter('opifer_form.form_edit_view'), [
            'form' => $form,
            'form_form' => $formType->createView(),
        ]);
    }

    /**
     * Delete a form.
     *
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $form = $this->get('opifer.form.form_manager')->getRepository()->find($id);

        if (!$form) {
            return $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($form);
        $em->flush();

        return $this->redirectToRoute('opifer_form_form_index');
    }

    /**
     * Submit a form.
     *
     * In case you would like to perform actions after the post is stored in the database,
     * you could create an EventListener that listens to the `Events::POST_FORM_SUBMIT` event.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function submitAction(Request $request, $id)
    {
        $form = $this->get('opifer.form.form_manager')->getRepository()->find($id);

        if (!$form) {
            throw $this->createNotFoundException('The form could not be found');
        }

        $post = $this->get('opifer.eav.eav_manager')->initializeEntity($form->getSchema());
        $post->setForm($form);

        $postForm = $this->createForm(PostType::class, $post, ['form_id' => $id]);
        $postForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $this->get('opifer.form.post_manager')->save($post);

            $event = new FormSubmitEvent($post);
            $this->get('event_dispatcher')->dispatch(Events::POST_FORM_SUBMIT, $event);
            
            if ($form->getRedirectUrl()) {
                return $this->redirect($form->getRedirectUrl());
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
