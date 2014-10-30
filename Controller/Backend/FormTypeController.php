<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Opifer\EavBundle\Entity\Attribute;
use Opifer\EavBundle\Entity\ValueType;

class FormTypeController extends Controller
{
    /**
     * Angular
     *
     * @param Request $request
     * @param array   $attributes
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function angularAction(Request $request, $attributes = array())
    {
        $attributes = $request->request->get('attributes');
        $form = $this->createFormBuilder([]);

        foreach ($attributes as $attribute) {
            $attributeEntity = new Attribute();
            $valueType = str_replace('Opifer\EavBundle\Entity', '\Opifer\EavBundle\Form\Type\ValueType', $attribute['valueType']) . 'Type';
            if (class_exists($valueType)) {
                $valueType = new $valueType($attributeEntity);
            } else {
                $valueType = new ValueType($attributeEntity);
            }

            $form->add($attribute['name'], $valueType, [
                'label' => $attribute['displayName'],
                'angular'  => [
                    'ng-model'  => 'subject.parameters[\''.$attribute["name"].'\']'
                ]
            ]);
        }
        $form = $form->getForm();

        return $this->render('OpiferContentBundle:Form:form.html.twig', ['form' => $form->createView()]);
    }
}
