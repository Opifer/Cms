<?php

namespace Opifer\FormBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Opifer\EavBundle\Model\EntityInterface;
use Opifer\EavBundle\Model\SchemaInterface;
use Opifer\EavBundle\Model\ValueSet;
use Opifer\EavBundle\Model\ValueSetInterface;

/**
 * Post entity.
 *
 * @ORM\MappedSuperclass
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Post implements PostInterface, EntityInterface
{
    /**
     * @var int
     *
     * @JMS\Expose
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Opifer\EavBundle\Model\ValueSet
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\ValueSetInterface", cascade={"persist"})
     * @ORM\JoinColumn(name="valueset_id", referencedColumnName="id")
     */
    protected $valueSet;

    /**
     * @var FormInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\FormBundle\Model\FormInterface", inversedBy="posts")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     */
    protected $form;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="submitted_at", type="datetime")
     */
    protected $submittedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param FormInterface $form
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Set valueSet.
     *
     * @param ValueSetInterface $valueSet
     *
     * @return Content
     */
    public function setValueSet(ValueSetInterface $valueSet = null)
    {
        $this->valueSet = $valueSet;

        return $this;
    }

    /**
     * Get valueSet.
     *
     * @return ValueSetInterface
     */
    public function getValueSet()
    {
        if ($this->valueSet === null) {
            $this->valueSet = new ValueSet();
        }

        return $this->valueSet;
    }

    /**
     * Get created at.
     *
     * @return \DateTime
     */
    public function getSubmittedAt()
    {
        return $this->submittedAt;
    }

    /**
     * Set submittedAt.
     *
     * @param \DateTime $deletedAt
     *
     * @return Post
     */
    public function setSubmittedAt($submittedAt)
    {
        $this->submittedAt = $submittedAt;

        return $this;
    }

    /**
     * Set deletedAt.
     *
     * @param \DateTime $deletedAt
     *
     * @return File
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt.
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set schema.
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
     * Get schema.
     *
     * @return SchemaInterface
     */
    public function getSchema()
    {
        return $this->getValueSet()->getSchema();
    }
}
