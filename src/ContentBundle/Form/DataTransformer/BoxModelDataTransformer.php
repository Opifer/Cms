<?php

namespace Opifer\ContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class BoxModelDataTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * Constructor
     *
     * @param string $prefix defines the difference between padding and margin
     */
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Splits the string into the side- and size values
     *
     * @param string|null $original
     * @return array|mixed
     */
    public function transform($original)
    {
        if (!$original) {
            return $original;
        }

        $string = substr($original, 1);
        $split = explode('-', $string);

        return [
            'side' => $split[0],
            'size' => $split[1],
        ];
    }

    /**
     * Combines the side- and size values into a class name
     *
     * @param array $submitted
     * @return string
     */
    public function reverseTransform($submitted)
    {
        return sprintf('%s%s-%s', $this->prefix, $submitted['side'], $submitted['size']);
    }
}
