<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Entity\Value;
use Opifer\EavBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class AttachmentValue extends Value
{
    /**
     * @var UploadedFile
     *
     * @Assert\File(maxSize="6000000")
     */
    protected $file;

    /**
     * @var MediaInterface
     *
     * @ORM\OneToOne(targetEntity="Opifer\EavBundle\Model\MediaInterface", cascade={"persist"})
     */
    protected $attachment;

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * @return MediaInterface
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param MediaInterface $attachment
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->getAttachment() ? $this->getAttachment()->getReference() : null;
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        $this->setAttachment($value);
    }

    /**
     * @inheritdoc
     */
    public function isEmpty()
    {
        return (is_null($this->getValue()) && is_null($this->getFile()));
    }

    public function __toString()
    {
        if ($this->getAttachment())
        {
            $reference = $this->attachment->getReference();
            if (empty($reference))
            {
                return '';
            }

            return $this->attachment->getReference();
        }

        return '';
    }
}
