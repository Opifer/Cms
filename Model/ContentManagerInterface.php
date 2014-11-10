<?php

namespace Opifer\ContentBundle\Model;

use Symfony\Component\HttpFoundation\Request;

interface ContentManagerInterface
{
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
     * @return ArrayCollection
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
}
