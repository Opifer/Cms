<?php

namespace Opifer\CmsBundle\Controller\Backend;

use Opifer\CmsBundle\Form\Type\ConfigType;
use Opifer\CmsBundle\Manager\ConfigManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ConfigController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var ConfigManager $configManager */
        $configManager = $this->get('opifer.cms.config_manager');

        $form = $this->createForm(ConfigType::class, []);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $config = [];
            foreach ($form->getData() as $group => $configuration) {
                foreach ($configuration as $key => $value) {
                    $config[$key] = $value;
                }
            }

            $em = $this->getDoctrine()->getManager();
            foreach ($config as $key => $value) {
                $item = $configManager->findOrCreate($key);
                $item->setValue($value);
                $em->persist($item);
            }

            $em->flush();

            $this->addFlash('success', 'flash.config_saved');

            return $this->redirectToRoute('opifer_cms_config_index');
        }

        return $this->render('OpiferCmsBundle:Backend/Config:index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
