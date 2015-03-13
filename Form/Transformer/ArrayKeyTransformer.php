<?php

namespace Opifer\EavBundle\Form\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayKeyTransformer implements DataTransformerInterface
{
    protected $key;

    /**
     * Constructor
     *
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Transforms an string to array with using key property
     *
     * @param string $string
     *
     * @return array
     */
    public function transform($string)
    {
        return [$this->key => $string];
    }

    /**
     * Transforms an array to string using key property
     *
     * @param array $array
     *
     * @return string
     */
    public function reverseTransform($array)
    {
        return $array[$this->key];
    }
}
