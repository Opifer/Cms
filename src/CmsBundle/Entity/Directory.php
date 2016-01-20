<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\ContentBundle\Model\Directory as BaseDirectory;

/**
 * Directory.
 *
 * @Gedmo\Tree(type="nested")
 * @GRID\Source(columns="id, name, slug")
 * @JMS\ExclusionPolicy("all")
 *
 * @ORM\Entity(repositoryClass="Opifer\CmsBundle\Repository\DirectoryRepository")
 * @ORM\Table(name="directory")
 */
class Directory extends BaseDirectory
{
    /**
     * @var string
     *
     * @JMS\Expose
     * @ORM\Column(name="name", type="string", length=128)
     * @Assert\NotBlank()
     * @Gedmo\Translatable
     */
    protected $name;

   /**
    * @ORM\Column(length=255, unique=true)
    *
    * @Gedmo\Slug(handlers={
    *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\TreeSlugHandler", options={
    *          @Gedmo\SlugHandlerOption(name="parentRelationField", value="parent"),
    *          @Gedmo\SlugHandlerOption(name="separator", value="/")
    *      })
    * }, fields={"name"}, unique_base="site")
    * @Gedmo\Translatable
    */
   protected $slug;

    /**
     * @Gedmo\Locale
     */
    protected $locale;

    /**
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    protected $site;

    /**
     * @var bool
     *
     * @ORM\Column(name="searchable", type="boolean")
     */
    protected $searchable = true;

    /**
     * Set translatable locale.
     *
     * @param string $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Set site.
     *
     * @param Site $site
     *
     * @return Directory
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site.
     *
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set searchable.
     *
     * @param bool $searchable
     *
     * @return Directory
     */
    public function setSearchable($searchable)
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * Get searchable.
     *
     * @return bool
     */
    public function getSearchable()
    {
        return $this->searchable;
    }
}
