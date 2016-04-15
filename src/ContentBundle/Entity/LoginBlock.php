<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\ContentBundle\Entity\Block;

/**
 * LoginBlock
 *
 * @ORM\Entity
 */
class LoginBlock extends Block
{
    /**
     * @var string
     *
     * @Revisions\Revised
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @var <Content>
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\ContentInterface")
     * @ORM\JoinColumn(name="login_redirect_content_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     *
     */
    protected $loginRedirectContentItem;

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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return NavigationBlock
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

   /**
     * Set login content
     *
     * @param Content $loginRedirectContentItem
     */
    public function setLoginRedirectContentItem($loginRedirectContentItem)
    {
        $this->loginRedirectContentItem = $loginRedirectContentItem;

        return $this;
    }

    /**
     * Get login redeirect content item
     *
     * @return Content
     */
    public function getLoginRedirectContentItem()
    {
        return $this->loginRedirectContentItem;
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
    public function getBlockType()
    {
        return 'login';
    }
}
