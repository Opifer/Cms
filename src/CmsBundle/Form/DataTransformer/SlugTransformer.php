<?php

namespace Opifer\CmsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class SlugTransformer implements DataTransformerInterface
{
    /**
     * Removes the directory path from the slug.
     *
     * @param string $slug
     *
     * @return string
     */
    public function transform($slug)
    {
        if (null === $slug) {
            return;
        }

        // If the slug ends with a slash, return just a slash
        // so the item is used as the index page of that directory
        if (substr($slug, -1) == '/') {
            return '/';
        }

        $array = explode('/', $slug);
        $slug = end($array);

        return $slug;
    }

    /**
     * Just return the slug. The directory path will be added by
     * the doctrine sluggable listener.
     *
     * @param string $slug
     *
     * @return string|null
     */
    public function reverseTransform($slug)
    {
        if (!$slug) {
            return;
        }

        return $slug;
    }
}
