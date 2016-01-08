<?php

namespace Opifer\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * MenuItem.
 *
 * @ORM\Table(name="menu")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 */
class MenuItem extends Menu
{
    /**
     * @ORM\ManyToOne(targetEntity="Content", fetch="EAGER")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=128)
     * @Gedmo\Translatable
     */
    private $link;

    /**
     * @var bool
     *
     * @ORM\Column(name="hidden_mobile", type="boolean", options={"default"=0})
     */
    protected $hiddenMobile = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="hidden_tablet_portrait", type="boolean", options={"default"=0})
     */
    protected $hiddenTabletPortrait = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="hidden_tablet_landscape", type="boolean", options={"default"=0})
     */
    protected $hiddenTabletLandscape = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="hidden_desktop", type="boolean", options={"default"=0})
     */
    protected $hiddenDesktop = 0;

    /**
     * @Gedmo\Locale
     */
    protected $locale;

    /**
     * @var array
     *
     * @ORM\Column(name="parameters", type="json_array", nullable=true)
     */
    protected $parameters;

    /**
     * Set link.
     *
     * @param string $link
     *
     * @return MenuItem
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set content.
     *
     * @param \Opifer\CmsBundle\Entity\Content $content
     *
     * @return MenuItem
     */
    public function setContent(\Opifer\CmsBundle\Entity\Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return \Opifer\CmsBundle\Entity\Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isHiddenMobile()
    {
        return (bool) $this->hiddenMobile;
    }

    /**
     * @param bool $hiddenMobile
     *
     * @return $this
     */
    public function setHiddenMobile($hiddenMobile)
    {
        $this->hiddenMobile = $hiddenMobile;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHiddenTabletPortrait()
    {
        return (bool) $this->hiddenTabletPortrait;
    }

    /**
     * @param bool $hiddenTabletPortrait
     *
     * @return $this
     */
    public function setHiddenTabletPortrait($hiddenTabletPortrait)
    {
        $this->hiddenTabletPortrait = $hiddenTabletPortrait;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHiddenTabletLandscape()
    {
        return (bool) $this->hiddenTabletLandscape;
    }

    /**
     * @param bool $hiddenTabletLandscape
     *
     * @return $this
     */
    public function setHiddenTabletLandscape($hiddenTabletLandscape)
    {
        $this->hiddenTabletLandscape = $hiddenTabletLandscape;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHiddenDesktop()
    {
        return (bool) $this->hiddenDesktop;
    }

    /**
     * @param bool $hiddenDesktop
     *
     * @return $this
     */
    public function setHiddenDesktop($hiddenDesktop)
    {
        $this->hiddenDesktop = $hiddenDesktop;

        return $this;
    }

    /**
     * Set $parameters.
     *
     * @param array $parameters
     *
     * @return MenuItem
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get $parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
