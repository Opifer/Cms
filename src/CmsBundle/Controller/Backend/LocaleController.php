<?php

namespace Opifer\CmsBundle\Controller\Backend;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Opifer\CmsBundle\Entity\Locale;
use Opifer\CmsBundle\Form\Type\LocaleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class LocaleController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('LOCALE_INDEX');

        $source = new Entity(Locale::class);

        $editAction = new RowAction('button.edit', 'opifer_cms_locale_edit');
        $editAction->setRouteParameters(['id']);

        /* @var $grid \APY\DataGridBundle\Grid\Grid */
        $grid = $this->get('grid');
        $grid->setId('locale')
            ->setSource($source)
            ->addRowAction($editAction)
        ;

        return $grid->getGridResponse('@OpiferCms/Backend/Locale/index.html.twig');
    }

    /**
     * Create a locale
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('LOCALE_CREATE');

        $locale = new Locale();

        $form = $this->createForm(new LocaleType(), $locale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($locale);
            $em->flush();

            $this->addFlash('success', 'Locale has been created successfully');

            return $this->redirectToRoute('opifer_cms_locale_edit', ['id' => $locale->getId()]);
        }

        return $this->render('@OpiferCms/Backend/Locale/create.html.twig', [
            'locale' => $locale,
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit a Locale
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null)
    {
        $this->denyAccessUnlessGranted('LOCALE_INDEX');

        $em = $this->getDoctrine()->getManager();

        if (is_numeric($id)) {
            $locale = $em->getRepository(Locale::class)->find($id);
        } else {
            $locale = $em->getRepository(Locale::class)->findOneByLocale($id);
        }

        $form = $this->createForm(new LocaleType(), $locale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($locale);
            $em->flush();

            $this->addFlash('success', 'Locale has been updated successfully');

            return $this->redirectToRoute('opifer_cms_locale_edit', ['id' => $locale->getId()]);
        }

        return $this->render('@OpiferCms/Backend/Locale/edit.html.twig', [
            'locale' => $locale,
            'form' => $form->createView()
        ]);
    }
}
