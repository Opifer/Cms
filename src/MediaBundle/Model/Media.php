<?php

namespace Opifer\MediaBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Media.
 *
 * @ORM\MappedSuperclass
 *
 * @UniqueEntity("reference")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
abstract class Media implements MediaInterface
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    const STATUS_HASPARENT = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    protected $status = self::STATUS_ENABLED;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @JMS\Expose
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $alt;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="provider", type="string", length=255)
     * @JMS\Expose
     */
    protected $provider;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255, unique=true)
     * @JMS\Expose
     */
    protected $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="content_type", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $contentType;

    /**
     * @var array
     *
     * @ORM\Column(name="metadata", type="json_array")
     * @JMS\Expose
     */
    protected $metadata;

    /**
     * Don't confuse this property with a smaller version of the existing image.
     * Thumb is used for media types that might need a thumbnail.
     * Like Videos, PDFs, etc.
     *
     * @var int
     *
     * @ORM\OneToOne(targetEntity="\Opifer\MediaBundle\Model\MediaInterface")
     * @ORM\JoinColumn(name="thumb_id", referencedColumnName="id")
     * @JMS\Expose
     */
    protected $thumb;

    /**
     * @var int
     *
     * @ORM\Column(name="filesize", type="integer", nullable=true)
     * @JMS\Expose
     */
    protected $filesize;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @JMS\Expose
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * This is just a temporary file holder, for file uploads through a form.
     *
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     *
     * @Assert\File(maxSize="6000000")
     */
    protected $file;

    /**
     * @var string
     */
    protected $original;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     *
     * @return Media
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        return $this;
    }

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
     * Set status.
     *
     * @param string $status
     *
     * @return Media
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Media
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     *
     * @return Media
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Set provider.
     *
     * @param string $provider
     *
     * @return Media
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider.
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set reference.
     *
     * @param string $reference
     *
     * @return Media
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference.
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set contentType.
     *
     * @param string $contentType
     *
     * @return Media
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get contentType.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get a short version of the content type.
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("file_type")
     *
     * @return string
     */
    public function getFileType()
    {
        return substr($this->contentType, strpos($this->contentType, '/') + 1);
    }

    /**
     * Set metadata.
     *
     * @param string $metadata
     *
     * @return Media
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Get metadata.
     *
     * @return string
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set thumb.
     *
     * @param string $thumb
     *
     * @return Media
     */
    public function setThumb(Media $thumb = null)
    {
        $this->thumb = $thumb;

        return $this;
    }

    /**
     * Get thumb.
     *
     * @return string
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Media
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Media
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set filesize.
     *
     * @param int $filesize
     *
     * @return Media
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;

        return $this;
    }

    /**
     * Get filesize.
     *
     * @return int
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * Get filesize with filesize extension.
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("readable_filesize")
     *
     * @return int|string
     */
    public function getReadableFilesize()
    {
        $size = $this->filesize;
        if ($size < 1) {
            return $size;
        }

        if ($size < 1024) {
            return $size.'b';
        } else {
            $help = $size / 1024;
            if ($help < 1024) {
                return round($help, 1).'kb';
            } else {
                return round(($help / 1024), 1).'mb';
            }
        }
    }

    /**
     * Set deletedAt.
     *
     * @param \DateTime $deletedAt
     *
     * @return Media
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
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set original.
     *
     * @param string $original
     *
     * @return Media
     */
    public function setOriginal($original)
    {
        $this->original = $original;

        return $this;
    }

    /**
     * Get original.
     *
     * @return string
     */
    public function getOriginal()
    {
        return $this->original;
    }
}
