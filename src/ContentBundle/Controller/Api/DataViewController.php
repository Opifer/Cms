<?php

namespace Opifer\ContentBundle\Controller\Api;

use Opifer\ContentBundle\Entity\DataView;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataViewController extends FOSRestController
{
    /**
     * @ParamConverter()
     * @ApiDoc()
     *
     * @param DataView $dataView
     *
     * @return DataView
     */
    public function getDataviewAction(DataView $dataView)
    {
        return $dataView;
    }

    /**
     * @ApiDoc()
     *
     * @return DataView[]
     */
    public function getDataviewsAction()
    {
        return $this->getDoctrine()->getRepository('OpiferContentBundle:DataView')->findAll();
    }

    /**
     * @ApiDoc()
     *
     * @param Request $request
     *
     * @return DataView
     */
    public function postDataviewsAction(Request $request)
    {
        $dataView = new DataView();
        $dataView->setText($request->request->get('text'));

        $errors = $this->get('validator')->validate($dataView);
        if (count($errors) > 0) {
            $errorStrings = [];
            foreach ($errors as $error) {
                $errorStrings[] = $error->getMessage();
            }
            return $this->view(
                [
                    'error' => implode(',', $errorStrings)
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->getDoctrine()->getManager()->persist($dataView);
        $this->getDoctrine()->getManager()->flush();

        return $dataView;
    }

    /**
     * @ApiDoc()
     *
     * @param DataView $dataView
     *
     * @return Response
     */
    public function deleteDataviewAction(DataView $dataView)
    {
        $this->getDoctrine()->getManager()->remove($dataView);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}