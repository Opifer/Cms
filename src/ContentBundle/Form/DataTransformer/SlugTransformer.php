<?php

namespace Opifer\ContentBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SlugTransformer implements DataTransformerInterface
{
    /**
     * Removes the directory path from the slug.
     *
     * @param  string $slug
     * @return string
     */
    public function transform($slug)
    {
        if (null === $slug) {
            return null;
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
     * @param  string $slug
     *
     * @return string|null
     */
    public function reverseTransform($slug)
    {
        if (!$slug) {
            return null;
        }

        return $slug;
    }
}