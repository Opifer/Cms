<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\CmsBundle\Entity\Content;

/**
 * TranslationGroup
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @ORM\Table(name="translation_group")
 * @ORM\Entity(repositoryClass="Opifer\ContentBundle\Repository\DataViewRepository")
 */
class TranslationGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Opifer\CmsBundle\Entity\Content", mappedBy="translationGroup")
     */
    private $contents;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $contents
     * @return $this
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param $locale
     * @return string
     */
    public function getSlug($locale)
    {
        $localeContent = null;
        foreach ($this->getContents() as $content) {
            /** @var Content $content */
            if ($content->getLocale() && $content->getLocale()->getLocale() === $locale) {
                $localeContent = $content;
                break;
            }
        }

        if ($localeContent !== null) {
            return '/' . $localeContent->getSlug();
        }

        return '/' . $locale;
    }

    public function getRouteMapping()
    {
        $mapping = [];
        foreach ($this->getContents() as $content) {
            if ($content->getLocale() && $locale = $content->getLocale()->getLocale()) {
                $mapping[$locale] = '/'.$content->getSlug();
            }
        }

        return $mapping;
    }
}
