<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\ContentBundle\Form\Type\ContentTypeType;
use Opifer\ContentBundle\Model\ContentTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ContentTypeController extends Controller
{
    /**
     * ContentType index view.
     *
     * The template defaults to `OpiferContentBundle:ContentType:index.html.twig`, but can easily be overwritten
     * in the bundle configuration.
     *
     * @Security("has_role('ROLE_CONTENT_MANAGER')")
     *
     * @return Response
     */
    public function indexAction()
    {
        $contentTypes = $this->get('opifer.content.content_type_manager')->getRepository()
            ->findAll();

        return $this->render($this->getParameter('opifer_content.content_type_index_view'), [
            'content_types' => $contentTypes,
        ]);
    }

    /**
     * Create a ContentType.
     *
     * @Security("has_role('ROLE_CONTENT_MANAGER')")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $contentTypeManager = $this->get('opifer.content.content_type_manager');

        $contentType = $contentTypeManager->create();

        $form = $this->createForm(ContentTypeType::class, $contentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData()->getSchema()->getAttributes() as $attribute) {
                $attribute->setSchema($contentType->getSchema());

                foreach ($attribute->getOptions() as $option) {
                    $option->setAttribute($attribute);
                }
            }

            $contentTypeManager->save($contentType);

            $this->addFlash('success', 'Content type has been created successfully');

            return $this->redirectToRoute('opifer_content_contenttype_edit', ['id' => $contentType->getId()]);
        }

        return $this->render($this->getParameter('opifer_content.content_type_create_view'), [
            'content_type' => $contentType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit a ContentType.
     *
     * @Security("has_role('ROLE_CONTENT_MANAGER')")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $contentTypeManager = $this->get('opifer.content.content_type_manager');
        $em = $this->get('doctrine.orm.entity_manager');

        /** @var ContentTypeInterface $contentType */
        $contentType = $contentTypeManager->getRepository()->find($id);

        if (!$contentType) {
            return $this->createNotFoundException();
        }

        $originalAttributes = new ArrayCollection();
        foreach ($contentType->getSchema()->getAttributes() as $attributes) {
            $originalAttributes->add($attributes);
        }

        $form = $this->createForm(ContentTypeType::class, $contentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Remove deleted attributes
            foreach ($originalAttributes as $attribute) {
                if (false === $contentType->getSchema()->getAttributes()->contains($attribute)) {
                    $em->remove($attribute);
                }
            }

            // Add new attributes
            foreach ($form->getData()->getSchema()->getAttributes() as $attribute) {
                $attribute->setSchema($contentType->getSchema());

                foreach ($attribute->getOptions() as $option) {
                    $option->setAttribute($attribute);
                }
            }

            $contentTypeManager->save($contentType);

            $this->addFlash('success', 'Content type has been updated successfully');

            return $this->redirectToRoute('opifer_content_contenttype_edit', ['id' => $contentType->getId()]);
        }

        return $this->render($this->getParameter('opifer_content.content_type_edit_view'), [
            'content_type' => $contentType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a ContentType.
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $contentType = $this->get('opifer.content.content_type_manager')->getRepository()->find($id);

        if (!$contentType) {
            return $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($contentType);
        $em->flush();

        return $this->redirectToRoute('opifer_content_contenttype_index');
    }
}
