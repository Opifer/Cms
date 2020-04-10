<?php

namespace Opifer\ReviewBundle\Controller;

use Opifer\ReviewBundle\Form\Type\ReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ReviewController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('REVIEW_INDEX');

        $reviews = $this->get('opifer.review.review_manager')->getRepository()->findAll();

        return $this->render($this->getParameter('opifer_review.review_index_view'), [
            'reviews' => $reviews,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('REVIEW_CREATE');

        $review = $this->get('opifer.review.review_manager')->createClass();

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();

            $this->addFlash('success', 'flash.review_created');

            return $this->redirectToRoute('opifer_review_review_edit', ['id' => $review->getId()]);
        }

        return $this->render($this->getParameter('opifer_review.review_create_view'), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('REVIEW_EDIT');

        $reviewManager = $this->get('opifer.review.review_manager');
        $review = $reviewManager->getRepository()->find($id);

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'flash.review_saved');

            return $this->redirectToRoute('opifer_review_review_edit', ['id' => $id]);
        }

        return $this->render($this->getParameter('opifer_review.review_edit_view'), [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete($id)
    {
        $this->denyAccessUnlessGranted('REVIEW_DELETE');

        $reviewManager = $this->get('opifer.review.review_manager');
        $review = $reviewManager->getRepository()->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($review);
        $em->flush();

        return $this->redirectToRoute('opifer_review_review_index');
    }
}
