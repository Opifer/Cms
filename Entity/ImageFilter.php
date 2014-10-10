<?php

namespace Opifer\MediaBundle\Entity;

use Opifer\MediaBundle\Validator\Constraint\ValidJson;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ImageFilter
 *
 * These filters are used by the LiipImagineBundle to determine how the images
 * should be rendered.
 *
 * @ORM\Table(name="imagefilter")
 * @ORM\Entity
 */
class ImageFilter
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-z\_]+$/i",
     *     message="only lowercase letters and _ characters are allowed"
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="quality", type="integer")
     * @Assert\Range(
     *     min = 1,
     *     max = 100,
     *     minMessage = "The quality must be at least 1",
     *     maxMessage = "The quality cannot be bigger than 100"
     * )
     */
    protected $quality;

    /**
     * @var string
     *
     * @ORM\Column(name="filters", type="json_array")
     * @ValidJson
     */
    protected $filters;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param  string      $name
     * @return ImageFilter
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set quality
     *
     * @param  string      $quality
     * @return ImageFilter
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return string
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set filter
     *
     * @param string $filters
     *
     * @return ImageFilter
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Tells whether the entity needs cache clearing after persisting
     */
    public function needsCacheClearing()
    {
        return true;
    }
}
