<?php

namespace Opifer\FormBundle\Controller\Api;

use Opifer\FormBundle\Form\Type\PostType;
use Opifer\FormBundle\Model\Form;
use Opifer\FormBundle\Model\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class FormController extends Controller
{
    /**
     * Get a definition of the given form
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function getFormAction($id)
    {
        /** @var Form $form */
        $form = $this->get('opifer.form.form_manager')->getRepository()->find($id);

        if (!$form) {
            throw $this->createNotFoundException('The form could not be found');
        }

        /** @var Post $post */
        $post = $this->get('opifer.eav.eav_manager')->initializeEntity($form->getSchema());
        $postForm = $this->createForm(PostType::class, $post, ['form_id' => $id]);

        $definition = $this->get('liform')->transform($postForm);

        /** @var CsrfTokenManagerInterface $csrf */
        $csrf = $this->get('security.csrf.token_manager');
        $token = $csrf->refreshToken($form->getName());

        // We're stuck with a legacy form structure here, which we'd like to hide on the API.
        $fields = $definition['properties']['valueset']['properties']['namedvalues']['properties'];
        $fields['_token'] = [
            'widget' => 'hidden',
            'value' => $token->getValue()
        ];

        return new JsonResponse($fields);
    }

    /**
     * Handle the creation of a form post
     *
     * @param Request $request
     * @param         $id
     *
     * @return JsonResponse
     */
    public function postFormPostAction(Request $request, $id)
    {
        /** @var Form $form */
        $form = $this->get('opifer.form.form_manager')->getRepository()->find($id);

        if (!$form) {
            throw $this->createNotFoundException('The form could not be found');
        }

        $data = json_decode($request->getContent(), true);
        // We're stuck with a legacy form structure here, which we'd like to hide on the API.
        $data = ['valueset' => ['namedvalues' => $data]];

        /** @var Post $post */
        $post = $this->get('opifer.eav.eav_manager')->initializeEntity($form->getSchema());
        $post->setForm($form);

        $postForm = $this->createForm(PostType::class, $post, ['form_id' => $id, 'csrf_protection' => false]);
        $postForm->submit($data);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $this->get('opifer.form.post_manager')->save($post);

            return new JsonResponse(['message' => 'Success'], 201);
        }

        return new JsonResponse([
            'errors' => (string) $postForm->getErrors()
        ], 400);
    }
}
