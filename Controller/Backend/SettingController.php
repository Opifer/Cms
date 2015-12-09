<?php

namespace Opifer\CmsBundle\Controller\Backend;

use Opifer\CmsBundle\Form\Type\SettingFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('OpiferCmsBundle:Setting')->findAll();

        $form = $this->createForm(new SettingFormType(), $settings);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $config = [];
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

            $this->addFlash('success', $this->get('translator')->trans('settings.edit.success', [
                '%url%' => $this->generateUrl('opifer.cms.cache_clear')
            ]));
        }

        return $this->render('OpiferCmsBundle:Backend/Setting:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
