<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Opifer\ContentBundle\Entity\Block;

/**
 * AvatarBlock
 *
 * @ORM\Entity
 */
class AvatarBlock extends Block
{
    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="text", name="value", nullable=true)
     */
    protected $loginUrl;

    /**
     * @var <Content>
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\ContentInterface")
     * @ORM\JoinColumn(name="login_content_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     *
     */
    protected $loginContentItem;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="text", name="registration_url", nullable=true)
     */
    protected $registrationUrl;

    /**
     * @var <Content>
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\ContentInterface")
     * @ORM\JoinColumn(name="registration_content_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     *
     */
    protected $registrationContentItem;


    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->loginUrl;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function setLoginUrl($loginUrl)
    {
        $this->loginUrl = $loginUrl;

        return $this;
    }

   /**
     * Set login content
     *
     * @param Content $loginContentItem
     */
    public function setLoginContentItem($loginContentItem)
    {
        $this->loginContentItem = $loginContentItem;

        return $this;
    }

    /**
     * Get login content item
     *
     * @return Content
     */
    public function getLoginContentItem()
    {
        return $this->loginContentItem;
    }

    /**
     * Set registration content
     *
     * @param Content $loginContentItem
     */
    public function setRegistrationContentItem($registrationContentItem)
    {
        $this->registrationContentItem = $registrationContentItem;

        return $this;
    }

    /**
     * Get registration content item
     *
     * @return Content
     */
    public function getRegistrationContentItem()
    {
        return $this->registrationContentItem;
    }

    /**
     * @return string
     */
    public function getRegistrationUrl()
    {
        return $this->registrationUrl;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function setRegistrationUrl($registrationUrl)
    {
        $this->registrationUrl = $registrationUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'avatar';
    }
}
