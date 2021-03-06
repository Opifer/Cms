<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\CmsBundle\Form\Type\GoogleAuthType;
use Opifer\CmsBundle\Form\Type\ProfileType;
use Opifer\CmsBundle\Form\Type\UserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends Controller
{
    /**
     * The user index
     * @return Response
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('USER_INDEX');

        $source = new Entity($this->container->getParameter('opifer_cms.user_model'));

        $editAction = new RowAction('edit', 'opifer_cms_user_edit');
        $editAction->setRouteParameters(['id']);

        //$deleteAction = new RowAction('delete', 'opifer_cms_user_delete');
        //$deleteAction->setRouteParameters(['id']);

        $grid = $this->get('grid');
        $grid->setId('users')
            ->setSource($source)
            ->addRowAction($editAction);
            //->addRowAction($deleteAction);

        return $grid->getGridResponse('@OpiferCms/Backend/User/index.html.twig');
    }

    /**
     * Create a new user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('USER_CREATE');

        $user = $this->getParameter('opifer_cms.user_model');
        $user = new $user();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('fos_user.user_manager')->updateUser($user, true);

            $this->addFlash('success', $this->get('translator')->trans('user.new.success', [
                '%username%' => ucfirst($user->getUsername()),
                '%id%' => $user->getId(),
            ]));

            return $this->redirectToRoute('opifer_cms_user_index');
        }

        return $this->render('@OpiferCms/Backend/User/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit a user.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('USER_EDIT');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('OpiferCmsBundle:User')->find($id);

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($user->isTwoFactorEnabled() == false) {
                $user->setGoogleAuthenticatorSecret(null);
            }

            $this->get('fos_user.user_manager')->updateUser($user, true);

            $this->addFlash('success', $this->get('translator')->trans('user.edit.success', [
                '%username%' => ucfirst($user->getUsername()),
            ]));

            return $this->redirectToRoute('opifer_cms_user_index');
        }

        return $this->render('@OpiferCms/Backend/User/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Edit the current users' profile.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function profileAction(Request $request)
    {
        $this->denyAccessUnlessGranted('USER_PROFILE');

        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($user->isTwoFactorEnabled() == false) {
                $user->setGoogleAuthenticatorSecret(null);
            }

            $this->get('fos_user.user_manager')->updateUser($user, true);

            if ($user->isTwoFactorEnabled() == true && empty($user->getGoogleAuthenticatorSecret())) {
                return $this->redirectToRoute('opifer_cms_user_activate_2fa');
            }

            $this->addFlash('success', 'Your profile was updated successfully!');

            return $this->redirectToRoute('opifer_cms_user_profile');
        }

        return $this->render('@OpiferCms/Backend/User/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Activate Google Authenticator
     *
     * @param Request $request
     *
     * @return Response
     */
    public function activateGoogleAuthAction(Request $request)
    {
        $user = $this->getUser();

        $secret = $this->container->get("scheb_two_factor.security.google_authenticator")->generateSecret();
        $user->setGoogleAuthenticatorSecret($secret);

        //Generate QR url
        $qrUrl = $this->container->get("scheb_two_factor.security.google_authenticator")->getUrl($user);

        $form = $this->createForm(GoogleAuthType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('fos_user.user_manager')->updateUser($user, true);

            $this->addFlash('success', 'Your profile was updated successfully!');

            return $this->redirectToRoute('opifer_cms_user_profile');
        }

        return $this->render('@OpiferCms/Backend/User/google_auth.html.twig', [
            'form' => $form->createView(),
            'secret' => $secret,
            'user' => $user,
            'qrUrl' => $qrUrl,
        ]);
    }
}
