<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Opifer\EavBundle\Entity\Value;
use Opifer\EavBundle\Model\SchemaInterface;
use Opifer\EavBundle\Model\ValueSetInterface;
use Opifer\EavBundle\Model\EntityInterface;
use Opifer\ContentBundle\Entity\Template;

/**
 * Content
 *
 * @ORM\MappedSuperclass
 *
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Tree(type="nested")
 */
class Content implements ContentInterface, EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     */
    protected $id;

    /**
     * @var ValueSetInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\ValueSetInterface", cascade={"persist"})
     * @ORM\JoinColumn(name="valueset_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $valueSet;

    /**
     * @var ContentType
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\ContentTypeInterface", inversedBy="content")
     * @ORM\JoinColumn(name="content_type_id", referencedColumnName="id")
     */
    protected $contentType;

    /**
     * @var boolean
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active = true;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @JMS\Expose
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Opifer\ContentBundle\Handler\AliasHandler", options={
     *          @Gedmo\SlugHandlerOption(name="field", value="slug"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="-")
     *      })
     * }, fields={"alias"}, unique_base="deletedAt")
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     *
     */
    protected $alias;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Opifer\ContentBundle\Handler\SlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="relationField", value="parent"),
     *          @Gedmo\SlugHandlerOption(name="relationSlugField", value="slug"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="/"),
     *          @Gedmo\SlugHandlerOption(name="onSlugCompletion", value={"appendIndex"})
     *      })
     * }, fields={"title"}, unique_base="deletedAt")
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

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
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * @var \Opifer\ContentBundle\Entity\Template
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\Template", fetch="EAGER")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     **/
    protected $template;

    /**
     * @var BlockInterface
     *
     * @ORM\OneToOne(targetEntity="Opifer\ContentBundle\Entity\Block", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id")
     **/
    protected $block;

    /**
     * @var SchemaInterface
     */
    public $schema;

    /**
     * @var ArrayCollection
     */
    protected $attributeValues;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attributeValues = new ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     * @return Content
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Content
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set slug
     *
     * @param  string $slug
     * @return Content
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get slug without index appended
     *
     * @return string
     */
    public function getBaseSlug()
    {
        $slug = $this->slug;

        if (substr($slug, -6) == '/index') {
            $slug = rtrim($slug, 'index');
        }

        return $slug;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Content
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return Content
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param ContentTypeInterface $contentType
     * @return Content
     */
    public function setContentType(ContentTypeInterface $contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Set lft
     *
     * @param  integer   $lft
     * @return Content
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param  integer   $lvl
     * @return Content
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param  integer   $rgt
     * @return Content
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param  integer   $root
     * @return Content
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent
     *
     * @param  ContentInterface $parent
     * @return ContentInterface
     */
    public function setParent(ContentInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return ContentInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param  ContentInterface $children
     * @return ContentInterface
     */
    public function addChild(ContentInterface $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove children
     *
     * @param  ContentInterface $children
     * @return ContentInterface
     */
    public function removeChild(ContentInterface $child)
    {
        $this->children->removeElement($child);

        return $this;
    }

    /**
     * Get children
     *
     * @return ContentInterface[]|\Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set created at
     *
     * @param  \DateTime $date
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $date)
    {
        $this->createdAt = $date;

        return $this;
    }

    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updated at
     *
     * @param  \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Set deletedAt
     *
     * @param  \DateTime $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set schema
     *
     * @param SchemaInterface $schema
     *
     * @return $this
     */
    public function setSchema(SchemaInterface $schema = null)
    {
        $this->getValueSet()->setSchema($schema);

        return $this;
    }

    /**
     * Get schema
     *
     * @return SchemaInterface
     */
    public function getSchema()
    {
        return $this->getValueSet()->getSchema();
    }

    /**
     * Add attributeValues
     *
     * @param Value $attributeValue
     *
     * @return $this
     */
    public function addAttributeValue(Value $attributeValue)
    {
        $this->attributeValues->add($attributeValue);

        return $this;
    }

    /**
     * Remove attributeValues
     *
     * @param Value $attributeValue
     */
    public function removeAttributeValue(Value $attributeValue)
    {
        $this->attributeValues->removeElement($attributeValue);
    }

    /**
     * Get attributeValues
     *
     * @return ArrayCollection
     */
    public function getAttributeValues()
    {
        return $this->attributeValues;
    }

    /**
     * Set valueSet
     *
     * @param ValueSetInterface $valueSet
     *
     * @return $this
     */
    public function setValueSet(ValueSetInterface $valueSet = null)
    {
        $this->valueSet = $valueSet;

        return $this;
    }

    /**
     * Get valueSet
     *
     * @return ValueSetInterface
     *
     * @throws \Exception
     */
    public function getValueSet()
    {
        return $this->valueSet;
    }

    /**
     * Gets the attributes and places them in an (by Twig) easily accessible array
     *
     * @return array
     */
    public function getAttributes()
    {
        $array = [];
        foreach ($this->getValueSet()->getValues() as $value) {
            $array[$value->getAttribute()->getName()] = $value;
        }

        return $array;
    }

    /**
     * Creates fake values for non-persisted attributes
     *
     * @return array new Values which can be persisted through an EntityManager
     */
    public function replaceMissingAttributes()
    {
        // collect persisted attributevalues
        $persistedAttributes = array();
        foreach ($this->getValueSet()->getValues() as $value) {
            $persistedAttributes[] = $value->getAttribute();
        }

        $newValues = array();
        // Create empty entities for missing attributes
        $missingAttributes = array_diff($this->getValueSet()->getAttributes()->toArray(), $persistedAttributes);
        foreach ($missingAttributes as $attribute) {
            $valueClass = $attribute->getValueType();
            $value = new $valueClass();

            $this->getValueSet()->addValue($value);
            $value->setValueSet($this->getValueSet());
            $value->setAttribute($attribute);

            $newValues[] = $value;
        }

        return $newValues;
    }

    /**
     * Returns name of the Schema for the ValueSet
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("schemaName")
     * @JMS\Groups({"detail"})
     *
     * @return array
     */
    public function getSchemaName()
    {
        return $this->getValueSet()->getSchema()->getName();
    }

    /**
     * Get breadcrumbs
     *
     * Loops through all parents to determine the breadcrumbs and stores them in
     * an associative array like [slug => label]
     *
     * @return array
     */
    public function getBreadCrumbs()
    {
        $crumbs = [];

        if (null !== $this->parent) {
            $crumbs = $this->getParent()->getBreadCrumbs();
        }

        $crumbs[$this->slug] = $this->getTitle();

        return $crumbs;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Template $template
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @return BlockInterface
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param BlockInterface $block
     */
    public function setBlock(BlockInterface $block)
    {
        $this->block = $block;
    }

}
