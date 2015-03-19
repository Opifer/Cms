<?php

namespace Opifer\ContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

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
     * Transforms an value to array with using key property
     *
     * @param mixed $value
     *
     * @return array
     */
    public function transform($value)
    {
        return [$this->key => $value];
    }

    /**
     * Transforms an array to value using key property
     *
     * @param array $array
     *
     * @return mixed
     */
    public function reverseTransform($array)
    {
        return $array[$this->key];
    }
}
