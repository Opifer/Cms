<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Locale
 */
class Locale
{
    /**
     * @var int
     *
     */
    protected $id;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $english_name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnglishName()
    {
        return $this->english_name;
    }

    /**
     * @param mixed $englishName
     */
    public function setEnglishName($english_name)
    {
        $this->english_name = $english_name;

        return $this;
    }
}