<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Opifer\EavBundle\Entity\NestedValue;
use Opifer\EavBundle\Entity\Value;
use Opifer\EavBundle\Model\EntityInterface;
use Opifer\EavBundle\Model\Nestable;
use Opifer\EavBundle\Model\TemplateInterface;
use Opifer\EavBundle\Model\ValueSetInterface;

/**
 * Content
 *
 * @ORM\MappedSuperclass
 *
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Content implements ContentInterface, EntityInterface, Nestable
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var ValueSetInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\ValueSetInterface", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="valueset_id", referencedColumnName="id")
     */
    protected $valueSet;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active = true;

    /**
     * @var string
     *
     * @JMS\Expose
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @var string
     *
     * @JMS\Expose
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @JMS\Expose
     * @ORM\Column(name="presentation", type="text", nullable=true)
     */
    protected $presentation;

    /**
     * @var string
     */
    protected $realPresentation;

    /**
     * @var string
     *
     * @JMS\Expose
     * @Gedmo\Slug(fields={"alias"}, unique_base="deletedAt")
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     *
     */
    protected $alias;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Content")
     * @ORM\JoinColumn(name="symlink", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    protected $symlink;

    /**
     * @var string
     *
     * @JMS\Expose
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Opifer\ContentBundle\Handler\SlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="relationField", value="directory"),
     *          @Gedmo\SlugHandlerOption(name="relationSlugField", value="slug"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="/"),
     *          @Gedmo\SlugHandlerOption(name="onSlugCompletion", value={"appendIndex"})
     *      })
     * }, fields={"title"}, unique_base="deletedAt")
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\DirectoryInterface")
     * @ORM\JoinColumn(name="directory_id", referencedColumnName="id")
     */
    protected $directory;

    /**
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Entity\NestedValue", inversedBy="nested")
     * @ORM\JoinColumn(name="nested_in", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $nestedIn;

    /**
     * @ORM\Column(name="nested_sort", type="integer", nullable=true)
     */
    protected $nestedSort;

    /**
     * Created at
     *
     * @var datetime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * Updated at
     *
     * @var datetime
     *
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
     * @var TemplateInterface
     */
    public $template;

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
     * For purpose of entity cloning
     */
    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
        };
    }

    /**
     * Set id
     * @param integer $id Id of choice
     * @return integer
     */
    public function setId($id=null)
    {
        return $this->id = $id;
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
     * @param  string  $title
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
     * @param  string  $description
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
     * @param  string  $slug
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

        if(substr($slug, -6) == '/index') {
            $slug = rtrim($slug, "index");
            $slug = rtrim($slug, "/");
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
     * Is content item public?
     *
     * @return boolean
     */
    public function isPublic()
    {
        return (is_null($this->nestedIn)) ? true : false;
    }

    /**
     * Is content item private?
     *
     * @return boolean
     */
    public function isPrivate()
    {
        return (!is_null($this->nestedIn)) ? true : false;
    }

    /**
     * Set directory
     *
     * @param DirectoryInterface $directory
     *
     * @return Content
     */
    public function setDirectory(DirectoryInterface $directory = null)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Get directory
     *
     * @return DirectoryInterface
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Set symlink
     *
     * @param ContentInterface $symlink
     *
     * @return Content
     */
    public function setSymlink(ContentInterface $symlink = null)
    {
        $this->symlink = $symlink;

        return $this;
    }

    /**
     * Get symlink
     *
     * @return ContentInterface
     */
    public function getSymlink()
    {
        return $this->symlink;
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
     * Set nested in
     *
     * @param NestedValue $value
     */
    public function setNestedIn(NestedValue $value)
    {
        $this->nestedIn = $value;

        return $this;
    }

    /**
     * Get nested in
     *
     * @return NestedValue
     */
    public function getNestedIn()
    {
        return $this->nestedIn;
    }

    /**
     * Get nested sort
     *
     * @return integer
     */
    public function getNestedSort()
    {
        return $this->nestedSort;
    }

    /**
     * Set nested in
     *
     * @param integer $value
     */
    public function setNestedSort($sort)
    {
        $this->nestedSort = (int) $sort;

        return $this;
    }

    /**
     * Set created at
     *
     * @param  \DateTime $date
     *
     * @return Content
     */
    public function setCreatedAt(\DateTime $date)
    {
        $this->createdAt = $date;

        return $this;
    }

    /**
     * Get created at
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated at
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param  \DateTime $deletedAt
     * @return File
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
     * Set template
     *
     * @param TemplateInterface $template
     *
     * @return Content
     */
    public function setTemplate(TemplateInterface $template = null)
    {
        $this->getValueSet()->setTemplate($template);

        return $this;
    }

    /**
     * Get template
     *
     * @return TemplateInterface
     */
    public function getTemplate()
    {
        return $this->getValueSet()->getTemplate();
    }

    /**
     * Set presentation
     *
     * @param string $presentation
     *
     * @return Template
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * For usage in frontend, use getRealPresentation() to avoid returning a 'null' value
     *
     * @return string
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Sets the real presentation
     *
     * If the presentation is different from the template presentation, it means
     * the presentation is edited for the current content, so we have to save
     * it on the content. Otherwise, leave it null
     *
     * @param string $presentation
     *
     * @return Content
     */
    public function setRealPresentation($presentation)
    {
        if ($presentation != $this->getTemplate()->getPresentation()) {
            $this->presentation = $presentation;
        }

        return $this;
    }

    /**
     * Get the real presentation.
     *
     * Returns the current presentation if its set. Otherwise it falls back
     * to the default presentation on the template
     *
     * @return string
     */
    public function getRealPresentation()
    {
        if (null != $this->presentation) {
            return $this->presentation;
        }

        return $this->getTemplate()->getPresentation();
    }

    /**
     * Add attributeValues
     *
     * @param Value $attributeValues
     *
     * @return Content
     */
    public function addAttributeValue(Value $attributeValue)
    {
        $this->attributeValues[] = $attributeValue;

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
     */
    public function getValueSet()
    {
        if (null === $this->valueSet) {
            throw new \Exception('Make sure to give Content a ValueSet on creation');
        }

        return $this->valueSet;
    }

    /**
     * Gets the attributes and places them in an (by Twig) easily accessable array
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
     * @todo clean this mess up
     *
     * Gets the attributes and places them in an (by Twig) easily accessable array
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("attributes")
     * @JMS\Groups({"elastica", "customgroup"})
     *
     * @return array
     */
    public function getPivotedAttributes()
    {
        $array = [];
        $array['coverImage'] = false;
        foreach ($this->getValueSet()->getValues() as $value) {
            switch (get_class($value)) {
                case 'Opifer\EavBundle\Entity\AddressValue':
                    $array[$value->getAttribute()->getName()] = [
                        'street' => $value->getAddress()->getStreet(),
                        'city' => $value->getAddress()->getCity(),
                        'country' => $value->getAddress()->getCountry(),
                        'location' => [
                            'lat' => floatval($value->getAddress()->getLat()),
                            'lon' => floatval($value->getAddress()->getLng())
                        ]
                    ];
                    break;
                case 'Opifer\EavBundle\Entity\CheckListValue':
                    $array[$value->getAttribute()->getName()] = array();
                    foreach ($value->getOptions() as $option) {
                        $array[$value->getAttribute()->getName()][] = [
                            'id' => $option->getId(),
                            'name' => $option->getName()
                        ];
                    }
                    break;
                case 'Opifer\EavBundle\Entity\MediaValue':
                    $array[$value->getAttribute()->getName()] = array();
                    foreach ($value->getMedias() as $media) {
                        if (!$array['coverImage']) {
                            $array['coverImage'] = $media->getReference();
                        }
                        $array[$value->getAttribute()->getName()][] = [
                            'name' => $media->getName(),
                            'file' => $media->getFile()
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
     * Returns name of the Template for the ValueSet
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("templateName")
     * @JMS\Groups({"elastica", "customgroup"})
     *
     * @return array
     */
    public function getTemplateName()
    {
        return $this->getValueSet()->getTemplate()->getName();
    }

    /**
     * Returns display name of the Template for the ValueSet
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("templateDisplayName")
     * @JMS\Groups({"elastica", "customgroup"})
     *
     * @return array
     */
    public function getTemplateDisplayName()
    {
        return $this->getValueSet()->getTemplate()->getDisplayName();
    }

    /**
     * Set defaults for nested content
     *
     * @return Content
     */
    public function setNestedDefaults()
    {
        // Override to set some defaults for nested content.

        return $this;
    }

    /**
     * Check if the content item has a nested content attribute
     *
     * @return boolean
     */
    public function getNestedContentAttributes()
    {
        $attributes = array();

        foreach ($this->getAttributes() as $key => $value) {
            if (get_class($value) == 'Opifer\EavBundle\Entity\NestedValue') {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
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

        if (null !== $this->directory) {
            $crumbs = $this->getDirectory()->getBreadCrumbs();
        }

        $crumbs[$this->slug] = $this->getTitle();

        return $crumbs;
    }
}
