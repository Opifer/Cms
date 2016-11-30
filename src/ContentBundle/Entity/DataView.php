<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DataView
 *
 * @ORM\Table(name="data_view")
 * @ORM\Entity(repositoryClass="Opifer\ContentBundle\Repository\DataViewRepository")
 */
class DataView
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
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9\_]+$/",
     *     match=true,
     *     message="Your name may only contain letters, numbers and underscores _. No whitespace allowed"
     * )
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="displayName", type="string", length=255, unique=true)
     */
    private $displayName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="viewCode", type="text", nullable=true)
     */
    private $viewCode;

    /**
     * @var string
     *
     * @ORM\Column(name="view", type="string", length=255, nullable=true)
     */
    private $viewFile;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="dataSources", type="object", nullable=true)
     */
    private $dataSources;

    /**
     * @var string
     *
     * @ORM\Column(name="iconType", type="string", length=64, nullable=true)
     */
    private $iconType;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return DataView
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     *
     * @return DataView
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return DataView
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
     * Set viewCode
     *
     * @param string $viewCode
     *
     * @return DataView
     */
    public function setViewCode($viewCode)
    {
        $this->viewCode = $viewCode;

        return $this;
    }

    /**
     * Get viewCode
     *
     * @return string
     */
    public function getViewCode()
    {
        return $this->viewCode;
    }

    /**
     * Set viewFile
     *
     * @param string $viewFile
     *
     * @return DataView
     */
    public function setViewFile($viewFile)
    {
        $this->viewFile = $viewFile;

        return $this;
    }

    /**
     * Get viewFile
     *
     * @return string
     */
    public function getViewFile()
    {
        return $this->viewFile;
    }

    /**
     * Set dataSources
     *
     * @param \stdClass $dataSources
     *
     * @return DataView
     */
    public function setDataSources($dataSources)
    {
        $this->dataSources = $dataSources;

        return $this;
    }

    /**
     * Get dataSources
     *
     * @return \stdClass
     */
    public function getDataSources()
    {
        return $this->dataSources;
    }

    /**
     * Set iconType
     *
     * @param string $iconType
     *
     * @return DataView
     */
    public function setIconType($iconType)
    {
        $this->iconType = $iconType;

        return $this;
    }

    /**
     * Get iconType
     *
     * @return string
     */
    public function getIconType()
    {
        return $this->iconType;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return DataView
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }
}

