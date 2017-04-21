<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Site
 *
 * @GRID\Source(columns="id, name, domains, defaultLocale")
 */
class Site
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var ArrayCollection|Domain[]
     *
     * @Assert\NotBlank()
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
     */
    private $defaultLocale;

    public function __construct()
    {
        $this->domains = new ArrayCollection();
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
     * @return string
     */
    public function getDomains()
    {
        return $this->domains;
    }

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
}
