<?php

namespace Opifer\ContentBundle\Model;

use Symfony\Component\HttpFoundation\Request;

interface ContentManagerInterface
{
    /**
     * Get the full class name of the Content entity
     *
     * @return string
     */
    public function getClass();

    /**
     * Initialize the content entity
     *
     * @return ContentInterface
     */
    public function initialize();

    /**
     * Save content
     *
     * @param  ContentInterface $content
     *
     * @return ContentInterface
     */
    public function save(ContentInterface $content);

    /**
     * Remove content
     *
     * @param array|integer $content
     */
    public function remove($content);

    /**
     * Get paginated items by request
     *
     * @param  Request $request
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPaginatedByRequest(Request $request);

    /**
     * Find one content item by its slug
     *
     * @param  string $slug
     *
     * @throws \Doctrine\ORM\NoResultException if content is not found
     *
     * @return ContentInterface
     */
    public function findOneBySlug($slug);

    /**
     * Get repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository();
}
