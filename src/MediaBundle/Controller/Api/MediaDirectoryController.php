<?php

namespace Opifer\MediaBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Opifer\MediaBundle\Entity\MediaDirectory;
use Opifer\MediaBundle\Entity\MediaDirectoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MediaDirectoryController extends FOSRestController
{
    /**
     * Get a single directory
     *
     * @ParamConverter()
     * @ApiDoc()
     *
     * @param MediaDirectory $directory
     *
     * @return MediaDirectoryInterface
     */
    public function getDirectoriesAction(MediaDirectory $directory)
    {
        return $directory;
    }

    /**
     * Create a new directory
     *
     * @ApiDoc()
     *
     * @RequestParam(name="name")
     * @RequestParam(name="parent", nullable=true)
     *
     * @return MediaDirectoryInterface
     */
    public function postDirectoriesAction(ParamFetcher $paramFetcher)
    {
        /** @var MediaDirectoryInterface $directory */
        $directory = $this->get('opifer.media.media_directory_manager')->create();
        $directory->setName($paramFetcher->get('name'));

        if ($paramFetcher->get('parent')) {
            $parent = $this->get('opifer.media.media_directory_manager')->getRepository()->find($paramFetcher->get('parent'));
            if ($parent) {
                $directory->setParent($parent);
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($directory);
        $em->flush();

        return $directory;
    }
}
