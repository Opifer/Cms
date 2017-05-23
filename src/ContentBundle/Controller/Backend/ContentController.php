<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Opifer\CmsBundle\Entity\Site;
use Opifer\CmsBundle\Manager\ContentManager;
use Opifer\ContentBundle\Form\Type\ContentType;
use Opifer\ContentBundle\Form\Type\LayoutType;
use Opifer\ContentBundle\Model\Content;
use Opifer\EavBundle\Manager\EavManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Backend Content Controller.
 */
class ContentController extends Controller
{
    /**
     * Index action.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render($this->getParameter('opifer_content.content_index_view'));
    }

    /**
     * @param int $type
     *
     * @return Response
     */
    public function typeAction($type)
    {
        $contentType = $this->get('opifer.content.content_type_manager')->getRepository()->find($type);

        $content = $this->get('opifer.content.content_manager')->getRepository()->findByType($contentType);

        return $this->render($this->getParameter('opifer_content.content_type_view'), [
            'content_type' => $contentType,
            'content' => $content,
        ]);
    }

    /**
     * Select the type of content before actually creating a new content item.
     *
     * @return Response|RedirectResponse
     */
    public function selectTypeAction($siteId = null)
    {
        $contentTypes = $this->get('opifer.content.content_type_manager')->getRepository()->findAll();

        $layouts = $this->get('opifer.content.content_manager')->getRepository()->findBy(['layout' => true]);

        if (!$contentTypes) {
            return $this->redirectToRoute('opifer_content_content_create');
        }

        return $this->render($this->getParameter('opifer_content.content_select_type'), [
            'content_types' => $contentTypes,
            'site_id' => $siteId,
            'layouts' => $layouts
        ]);
    }

    public function selectSiteAction()
    {
        $sites = $this->getDoctrine()->getRepository(Site::class)->findAll();

        //if no site go on
        if (count($sites) == 0) {
            return $this->redirectToRoute('opifer_content_content_select_type');
        }

        //if just one site select this one
        if (count($sites) == 1) {
            return $this->redirectToRoute('opifer_content_content_select_type', ['siteId' => $sites[0]->getId()]);
        }

        return $this->render($this->getParameter('opifer_content.content_select_site'), [
            'sites' => $sites
        ]);
    }

    /**
     * Select the type of content before actually creating a new content item.
     *
     * @return Response|RedirectResponse
     */
    public function selectLayoutTypeAction()
    {
        $contentTypes = $this->get('opifer.content.content_type_manager')->getRepository()->findAll();

        if (!$contentTypes) {
            return $this->redirectToRoute('opifer_cms_layout_create');
        }

        return $this->render($this->getParameter('opifer_content.content_select_layout_type'), [
            'content_types' => $contentTypes,
        ]);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse|Response
     */
    public function editTypeAction(Request $request, $id)
    {
        /** @var ContentManager $manager */
        $manager = $this->get('opifer.content.content_manager');

        /** @var Content $content */
        $content = $manager->getRepository()->find($id);

        $form = $this->createFormBuilder($content)
            ->add('contentType', EntityType::class, [
                'class' => $this->get('opifer.content.content_type_manager')->getClass(),
                'choice_label' => 'name',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EavManager $eavManager */
            $eavManager = $this->get('opifer.eav.eav_manager');
            $valueSet = $eavManager->createValueSet();

            $this->getDoctrine()->getManager()->persist($valueSet);

            $valueSet->setSchema($content->getContentType()->getSchema());
            $content->setValueSet($valueSet);

            $manager->save($content);

            return $this->redirectToRoute('opifer_content_content_edit', ['id' => $content->getId()]);
        }

        return $this->render($this->getParameter('opifer_content.content_edit_type'), [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Create a new content item.
     *
     * @param Request $request
     * @param int     $type
     *
     * @return Response
     */
    public function createAction(Request $request, $siteId = null, $type = 0, $layoutId = null)
    {
        /** @var ContentManager $manager */
        $manager = $this->get('opifer.content.content_manager');

        if ($type) {
            $contentType = $this->get('opifer.content.content_type_manager')->getRepository()->find($type);
            $content = $this->get('opifer.eav.eav_manager')->initializeEntity($contentType->getSchema());
            $content->setContentType($contentType);
        } else {
            $content = $manager->initialize();
        }

        if ($siteId) {
            $site = $this->getDoctrine()->getRepository(Site::class)->find($siteId);

            //set siteId on content item
            $content->setSite($site);
            if ($site->getDefaultLocale()) {
                $content->setLocale($site->getDefaultLocale());
            }
        }

        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($layoutId) {
                $duplicatedContent = $this->duplicateAction($layoutId, $content);

                return $this->redirectToRoute('opifer_content_contenteditor_design', [
                    'owner' => 'content',
                    'ownerId' => $duplicatedContent->getId(),
                ]);
            }

            if (null === $content->getPublishAt()) {
                $content->setPublishAt(new \DateTime());
            }

            $manager->save($content);

            return $this->redirectToRoute('opifer_content_contenteditor_design', [
                'owner' => 'content',
                'ownerId' => $content->getId(),
                'site_id' => $siteId
            ]);
        }

        return $this->render($this->getParameter('opifer_content.content_new_view'), [
            'form' => $form->createView(),
        ]);
    }

    public function duplicateAction($id, $content)
    {
        /** @var ContentManagerInterface $contentManager */
        $contentManager = $this->get('opifer.content.content_manager');
        $layout = $contentManager->getRepository()->find($id);

        if (!$layout) {
            throw $this->createNotFoundException('No layout found for id '.$id);
        }

        /** @var BlockManager $blockManager */
        $blockManager = $this->container->get('opifer.content.block_manager');

        $duplicatedContent = $contentManager->duplicate($layout);

        $duplicatedContent->setSlug($content->getSlug());
        $duplicatedContent->setTitle($content->getTitle());
        $duplicatedContent->setAuthor($content->getAuthor());
        $duplicatedContent->setAlias($content->getAlias());
        $duplicatedContent->setIndexable($content->getIndexable());
        $duplicatedContent->setActive($content->getActive());
        $duplicatedContent->setSearchable($content->getSearchable());
        $duplicatedContent->setParent($content->getParent());
        $duplicatedContent->setShowInNavigation($content->showInNavigation());
        $duplicatedContent->setShortTitle($content->getShortTitle());
        $duplicatedContent->setDescription($content->getDescription());

        $duplicatedContent->setLayout(0);

        $this->getDoctrine()->getManager()->flush($duplicatedContent);

        $contentBlocks = $blockManager->duplicate($layout->getBlocks(), $duplicatedContent);

        $duplicatedContent->setBlocks($contentBlocks);
        $this->getDoctrine()->getManager()->flush($duplicatedContent);

        return $duplicatedContent;
    }

    /**
     * Edit the details of Content.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        /** @var ContentManager $manager */
        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);
        $content = $manager->createMissingValueSet($content);

        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if (null === $content->getPublishAt()) {
                $content->setPublishAt($content->getCreatedAt());
            }

            $manager->save($content);

            return $this->redirectToRoute('opifer_content_content_index');
        }

        return $this->render($this->getParameter('opifer_content.content_edit_view'), [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Details action for an inline form in the Content Design.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsAction(Request $request, $id)
    {
        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);
        $content = $manager->createMissingValueSet($content);

        if ($content->getLayout()) {
            $form = $this->createForm(LayoutType::class, $content);
        } else {
            $form = $this->createForm(ContentType::class, $content);
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            if (null === $content->getPublishAt()) {
                $content->setPublishAt($content->getCreatedAt());
            }

            $manager->save($content);
        }

        return $this->render($this->getParameter('opifer_content.content_details_view'), [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    public function historyAction(Request $request, $owner, $ownerId)
    {
        return $this->render($this->getParameter('opifer_content.content_history_view'), array());
    }
}
