<?php

namespace Opifer\CmsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Opifer\CmsBundle\Form\SettingForm;

class SettingController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/settings", name="opifer.cms.settings")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('OpiferCmsBundle:Setting')->findAll();

        $form = $this->createForm(new SettingForm(), $settings);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $config = array();
            foreach ($form->getData() as $key => $value) {
                if (!is_int($key)) {
                    $config[$key] = $value;
                }
            }

            foreach ($settings as $setting) {
                $name = $setting->getExtension().'_'.str_replace('.', '_', $setting->getName());

                $setting->setValue($config[$name]);
            }
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                $this->get('translator')->trans('settings.edit.success', ['%url%' => $this->generateUrl('opifer.cms.cache_clear')])
            );
        }

        return $this->render('OpiferCmsBundle:Setting:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
