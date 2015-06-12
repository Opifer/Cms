<?php

namespace Opifer\CmsBundle\Controller;

use Knp\Menu\MenuFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Opifer\CmsBundle\Entity\Menu;
use Opifer\CmsBundle\Entity\MenuGroup;
use Opifer\CmsBundle\Entity\MenuItem;

class MenuController extends Controller
{
    /**
     * Create a new menu or menuitem.
     *
     * @Route(
     *     "/menu/create/{type}/{locale}",
     *     name="opifer_cms_menu_create"
     * )
     *
     * @param Request $request
     * @param integer $id
     * @param string  $type
     * @param string  $locale
     *
     * @return Response
     */
    public function createAction(Request $request, $type = 'item', $locale = null)
    {
        $em = $this->getDoctrine()->getManager();
        $locales = $this->container->getParameter('opifer_cms.allowed_locales');
        $defaultLocale = $this->container->getParameter('locale');
        $tr = $this->get('translator');

        if ($type == 'group') {
            $menu = new MenuGroup();
        } else {
            $menu = new MenuItem();
        }

        if (count($locales) > 1) {
            $factory = new MenuFactory();
            $localeMenu = $factory->createItem('localeMenu');

            $localeMenu->addChild('default', array(
                'uri' => $this->generateUrl('opifer_cms_menu_edit', [
                    'id' => ($menu->getId()) ? $menu->getId() : 0,
                ]),
                'label' => $tr->trans('Default'),
            ));

            foreach ($locales as $plocale) {
                if ($plocale === $defaultLocale) {
                    continue;
                }

                $localeMenu->addChild($plocale, [
                    'uri' => $this->generateUrl('opifer_cms_menu_edit', [
                        'id' => ($menu->getId()) ? $menu->getId() : 0,
                        'locale' => $plocale,
                    ]),
                    'label' => $tr->trans($plocale),
                ]);
            }

            foreach ($localeMenu->getChildren() as $localeMenuChild) {
                if ($localeMenuChild->getName() == $locale) {
                    $localeMenuChild->setCurrent(true);
                }
                if ($localeMenuChild->getName() == 'default' && $locale == null) {
                    $localeMenuChild->setCurrent(true);
                }
            }
        }

        $site = $this->get('doctrine')->getRepository('OpiferCmsBundle:Site')->findOneBy([]);

        $menu->setSite($site);

        $form = $this->createForm('admin_menu', $menu);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($menu);
            $em->flush();

            return $this->redirect($this->generateUrl('opifer_cms_menu_index', [
                'siteId' => $menu->getSite()->getId(),
                'locale' => $locale,
            ]));
        }

        return $this->render('OpiferCmsBundle:Menu:edit.html.twig', [
            'menu'       => $menu,
            'localeMenu' => (isset($localeMenu)) ? $localeMenu : '',
            'form'       => $form->createView(),
        ]);
    }

    /**
     * Edit an existing menu item.
     *
     * @Route(
     *     "/menu/edit/{id}/{type}/{locale}",
     *     name="opifer_cms_menu_edit"
     * )
     *
     * @param Request $request
     * @param integer $id
     * @param string  $type
     * @param string  $locale
     *
     * @return Response
     */
    public function editAction(Request $request, $id = 0, $type = 'item', $locale = null)
    {
        $em = $this->getDoctrine()->getManager();
        $locales = $this->container->getParameter('opifer_cms.allowed_locales');
        $defaultLocale = $this->container->getParameter('locale');
        $tr = $this->get('translator');

        $menu = $this->get('doctrine')->getRepository('OpiferCmsBundle:Menu')
            ->findOneById($id);

        if ($locale !== null) {
            $menu->setTranslatableLocale($locale);
            $em->refresh($menu);
        }

        if (!$menu) {
            throw $this->createNotFoundException('No menu found for id '.$id);
        }

        if (count($locales) > 1) {
            $factory = new MenuFactory();
            $localeMenu = $factory->createItem('localeMenu');

            $localeMenu->addChild('default', array(
                'uri' => $this->generateUrl('opifer_cms_menu_edit', [
                    'id' => ($menu->getId()) ? $menu->getId() : 0,
                ]),
                'label' => $tr->trans('Default'),
            ));

            foreach ($locales as $plocale) {
                if ($plocale === $defaultLocale) {
                    continue;
                }

                $localeMenu->addChild($plocale, [
                    'uri' => $this->generateUrl('opifer_cms_menu_edit', [
                        'id' => ($menu->getId()) ? $menu->getId() : 0,
                        'locale' => $plocale,
                    ]),
                    'label' => $tr->trans($plocale),
                ]);
            }

            foreach ($localeMenu->getChildren() as $localeMenuChild) {
                if ($localeMenuChild->getName() == $locale) {
                    $localeMenuChild->setCurrent(true);
                }
                if ($localeMenuChild->getName() == 'default' && $locale == null) {
                    $localeMenuChild->setCurrent(true);
                }
            }
        }

        $form = $this->createForm('admin_menu', $menu);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($menu);
            $em->flush();

            return $this->redirect($this->generateUrl('opifer_cms_menu_index', [
                'locale' => $locale,
            ]));
        }

        return $this->render('OpiferCmsBundle:Menu:edit.html.twig', [
            'menu'       => $menu,
            'localeMenu' => (isset($localeMenu)) ? $localeMenu : '',
            'form'       => $form->createView(),
        ]);
    }

    /**
     * Delete an existing menu item.
     *
     * @Route(
     *     "/menu/delete/{id}",
     *     name="opifer_cms_menu_delete"
     * )
     *
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $menu = $this->get('doctrine')->getRepository('OpiferCmsBundle:Menu')
            ->findOneById($id);

        if (!$menu) {
            throw $this->createNotFoundException('No menu found for id '.$id);
        }

        $em->remove($menu);
        $em->flush();

        return $this->redirect($this->generateUrl('opifer_cms_menu_index', [
            'locale' => null,
        ]));
    }

    /**
     * Index action.
     *
     * @Route(
     *     "/menu/{locale}",
     *     name="opifer_cms_menu_index"
     * )
     */
    public function indexAction($locale = null)
    {
        $menuTree = $this->getDoctrine()
            ->getRepository('OpiferCmsBundle:Menu')
            ->childrenHierarchy();

        $site = $this->getDoctrine()
            ->getRepository('OpiferCmsBundle:Site')
            ->findOneBy([]);

        if (!$site) {
            throw new ResourceNotFoundException('There is no site found');
        }

        return $this->render('OpiferCmsBundle:Menu:index.html.twig', [
            'menuTree' => $menuTree,
            'site'     => $site,
        ]);
    }
}
