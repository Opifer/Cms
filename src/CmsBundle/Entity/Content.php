<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\ContentBundle\Model\Content as BaseContent;
use Opifer\ContentBundle\Model\ContentTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Content.
 *
 * @ORM\Entity(repositoryClass="Opifer\CmsBundle\Repository\ContentRepository")
 * @ORM\Table(name="content")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\Tree(type="nested")
 * @GRID\Source(columns="id, title, slug")
 * @Revisions\Revision
 */
class Content extends BaseContent
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\ContentTypeInterface", inversedBy="content")
     * @ORM\JoinColumn(name="content_type_id", referencedColumnName="id")
     */
    protected $contentType;

    /**
     * @var bool
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active = true;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="FOS\UserBundle\Model\UserInterface", inversedBy="contents")
     */
    protected $author;

    /**
     * @var bool
     *
     * @ORM\Column(name="indexable", type="boolean")
     */
    protected $indexable = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="searchable", type="boolean")
     */
    protected $searchable = true;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     * @Revisions\Revised
     */
    protected $title;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Revisions\Revised
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Opifer\CmsBundle\Entity\Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    protected $site;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    protected $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    protected $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    protected $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    protected $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\ContentInterface", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Model\ContentInterface", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    /**
     * Created at.
     *
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * Updated at.
     *
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @Gedmo\Locale
     */
    protected $locale;

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
     * Set indexable
     *
     * @param bool $indexable
     *
     * @return Content
     */
    public function setIndexable($indexable)
    {
        $this->indexable = $indexable;

        return $this;
    }

    /**
     * Get indexable.
     *
     * @return bool
     */
    public function getIndexable()
    {
        return $this->indexable;
    }

    /**
     * Is indexable.
     *
     * @return bool
     */
    public function isIndexable()
    {
        return ($this->indexable) ? true : false;
    }

    /**
     * Set searchable.
     *
     * @param bool $searchable
     *
     * @return Content
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

    /**
     * Is searchable.
     *
     * @return bool
     */
    public function isSearchable()
    {
        return ($this->searchable) ? true : false;
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
        foreach ($this->getValueSet()->getValues() as $value) {
            switch (get_class($value)) {
                case 'Opifer\EavBundle\Entity\CheckListValue':
                    $array[$value->getAttribute()->getName()] = array();
                    foreach ($value->getOptions() as $option) {
                        $array[$value->getAttribute()->getName()][] = [
                            'id' => $option->getId(),
                            'name' => $option->getName(),
                        ];
                    }
                    break;
                case 'Opifer\EavBundle\Entity\MediaValue':
                    $array[$value->getAttribute()->getName()] = array();
                    foreach ($value->getMedias() as $media) {
                        $array[$value->getAttribute()->getName()][] = [
                            'name' => $media->getName(),
                            'file' => $media->getFile(),
                        ];
                    }
                    break;
                default:
                    $array[$value->getAttribute()->getName()] = $value->getValue();
            }
        }

        return $array;
    }
}
