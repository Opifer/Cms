<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Site
 *
 * @JMS\ExclusionPolicy("all")
 * @GRID\Source(columns="id, name, domains, defaultLocale")
 */
class Site
{
    /**
     * @var int
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    private $name;

    /**
     * @var string
     * @JMS\Expose
     */
    private $description;

    /**
     * @var ArrayCollection|Domain[]
     *
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    private $domains;

    /**
     * @var string
     */
    private $cookieDomain;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    private $defaultLocale;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    private $defaultDomain;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Model\ContentInterface", mappedBy="contentType")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $content;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->domains = new ArrayCollection();
        $this->content = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Site
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
     * Set description.
     *
     * @param string $description
     *
     * @return Site
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     *  Set domains.
     *
     * @param ArrayCollection $domains
     *
     * @return Site
     */
    public function setDomains($domains)
    {
        $this->domains = $domains;

        return $this;
    }

    /**
     * Get domains.
     *
     * @return ArrayCollection|Domain[]
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * @param Domain $domain
     *
     * @return $this
     */
    public function addDomain(Domain $domain)
    {
        $domain->setSite($this);

        $this->domains[] = $domain;

        return $this;
    }

    public function removeDomain(Domain $domain)
    {
        $this->domains->removeElement($domain);

        return $this;
    }

    /**
     * Set cookieDomain.
     *
     * @param string $cookieDomain
     *
     * @return Site
     */
    public function setCookieDomain($cookieDomain)
    {
        $this->cookieDomain = $cookieDomain;

        return $this;
    }

    /**
     * Get cookieDomain.
     *
     * @return string
     */
    public function getCookieDomain()
    {
        return $this->cookieDomain;
    }

    /**
     * Set defaultLocale.
     *
     * @param string $defaultLocale
     *
     * @return Site
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }

    /**
     * Get defaultLocale.
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * Set defaultDomain.
     *
     * @param string $defaultDomain
     *
     * @return Site
     */
    public function setDefaultDomain($defaultDomain)
    {
        $this->defaultDomain = $defaultDomain;

        return $this;
    }

    /**
     * Get defaultDomain.
     *
     * @return string
     */
    public function getDefaultDomain()
    {
        if ($this->defaultDomain) {
            return $this->defaultDomain;
        } elseif ($first = $this->getDomains()->first()) {
            return $first->getDomain();
        } else {
            return null;
        }
    }
}
