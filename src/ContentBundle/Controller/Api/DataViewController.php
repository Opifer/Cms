<?php

namespace Opifer\ContentBundle\Controller\Api;

use Opifer\ContentBundle\Entity\DataView;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\NoRoute;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
    public function getDataview(DataView $dataView)
    {
        return $dataView;
    }

    /**
     * @ApiDoc()
     *
     * @return DataView[]
     */
    public function getDataviews()
    {
        return $this->getDoctrine()->getRepository('OpiferContentBundle:DataView')->findAll();
    }

    /**
     * @ApiDoc()
     * @Put("/dataviews")
     *
     * @ParamConverter("dataView", converter="fos_rest.request_body", options={"validator",{"groups"={"detail"}}})
     */
    public function putDataview(DataView $dataView, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($dataView);
        $em->flush();

        $cacheDriver = $em->getConfiguration()->getResultCacheImpl();
        $cacheDriver->deleteAll();

        $view = $this->view(['message' => 'saved'], 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc()
     *
     * @param DataView $dataView
     *
     * @return Response
     */
    public function deleteDataview(DataView $dataView)
    {
        $this->getDoctrine()->getManager()->remove($dataView);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }


    private function handleValidationErrors(ConstraintViolationListInterface $validationErrors)
    {
        $errors = array();

        foreach ($validationErrors as $validationError) {
            $errors[$validationError->getPropertyPath()] = $validationError->getMessage();
        }

        $this->normalize($errors);
        $view = $this->view(['errors' => $errors], 400);

        return $this->handleView($view);
    }


    public function normalize(array &$data)
    {
        $normalizedData = array();

        foreach ($data as $key => $val) {

            $snakeCasedName = '';

            $len = strlen($key);
            for ($i = 0; $i < $len; ++$i) {
                if (ctype_upper($key[$i])) {
                    $snakeCasedName .= '_'.strtolower($key[$i]);
                } else {
                    $snakeCasedName .= strtolower($key[$i]);
                }
            }

            $normalizedData[$snakeCasedName] = $val;
            $key = $snakeCasedName;

            if (is_array($val)) {
                $this->normalize($normalizedData[$key]);
            }
        }

        $data = $normalizedData;
    }
}