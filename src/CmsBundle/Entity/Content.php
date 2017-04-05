<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\ContentBundle\Model\Content as BaseContent;
use Opifer\ContentBundle\Model\ContentTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Content.
 *
 * @JMS\ExclusionPolicy("all")
 * @GRID\Source(columns="id, title, slug, alias, active, updatedAt, indexable, searchable")
 */
class Content extends BaseContent
{
    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @GRID\Column(title="Id", size="10", type="number")
     */
    protected $id;

    /**
     * @var ContentTypeInterface
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     */
    protected $contentType;

    /**
     * @var bool
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     */
    protected $active = true;

    /**
     * @var User
     */
    protected $author;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Assert\NotBlank()
     * @Revisions\Revised
     */
    protected $title;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Revisions\Revised
     */
    protected $description;

    /**
     * @var MediaInterface[]
     *
     * @JMS\Expose
     */
    protected $medias;

    /**
     * @var bool
     *
     * @GRID\Column(title="Indexable", type="boolean", visible=false)
     */
    protected $indexable = true;

    /**
     * @var bool
     *
     * @GRID\Column(title="Searchable", type="boolean", visible=false)
     */
    protected $searchable = true;

    /**
     * @var bool

     * @GRID\Column(title="Layout", type="boolean", visible=false)
     */
    protected $layout = false;

    /**
     * @var string
     */
    protected $layoutPreview;

    /**
     */
    protected $site;

    /**
     * @JMS\Expose
     */
    protected $children;

    /**
     * Created at.
     *
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Revisions\Revised
     */
    protected $createdAt;

    /**
     * Updated at.
     *
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     */
    protected $updatedAt;

    /**
     * @Gedmo\Locale
     */
    protected $locale;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->medias = new ArrayCollection();
    }

    /**
     * Sets an author on for the content.
     *
     * @var User
     *
     * @return Content
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set site.
     *
     * @param Site $site
     *
     * @return Content
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site.
     *
     * @return \Opifer\CmsBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return MediaInterface[]|ArrayCollection
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @param MediaInterface[]|ArrayCollection $medias
     *
     * @return Content
     */
    public function setMedias($medias)
    {
        $this->medias = $medias;

        return $this;
    }

    /**
     * @param MediaInterface $media
     *
     * @return Content
     */
    public function addMedia(MediaInterface $media)
    {
        $this->medias[] = $media;

        return $this;
    }

    /**
     * @param MediaInterface $media
     *
     * @return Content
     */
    public function removeMedia(MediaInterface $media)
    {
        $this->medias->removeElement($media);

        return $this;
    }

    /**
     * @todo clean this mess up
     *
     * Gets the attributes and places them in an (by Twig) easily accessible array
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("attributes")
     * @JMS\Groups({"detail"})
     * @JMS\MaxDepth(2)
     *
     * @return array
     */
    public function getPivotedAttributes()
    {
        $array = [];

        if ($this->getValueSet() === null) {
            return $array;
        }

        foreach ($this->getValueSet()->getValues() as $value) {
            switch (get_class($value)) {
                case 'Opifer\EavBundle\Entity\CheckListValue':
                    $array[$value->getAttribute()->getName()] = [];
                    foreach ($value->getOptions() as $option) {
                        $array[$value->getAttribute()->getName()][] = [
                            'id' => $option->getId(),
                            'name' => $option->getName(),
                        ];
                    }
                    break;
                default:
                    $array[$value->getAttribute()->getName()] = $value->getValue();
            }
        }

        return $array;
    }

    /**
     * @return \Opifer\CmsBundle\Entity\Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param Locale $locale
     * @return $this
     */
    public function setLocale(Locale $locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
