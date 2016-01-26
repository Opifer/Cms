<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Opifer\EavBundle\Model\SchemaInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ContentType implements ContentTypeInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     */
    protected $name;

    /**
     * @var SchemaInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\SchemaInterface", cascade={"persist"})
     * @ORM\JoinColumn(name="schema_id", referencedColumnName="id")
     */
    protected $schema;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Model\ContentInterface", mappedBy="contentType")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $content;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->content = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return SchemaInterface
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param SchemaInterface $schema
     */
    public function setSchema(SchemaInterface $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @return ContentInterface[]|ArrayCollection
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function addPost(ContentInterface $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function removePost(ContentInterface $content)
    {
        $this->content->removeElement($content);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
}
