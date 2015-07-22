<?php

namespace Opifer\CmsBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Opifer\CrudBundle\Annotation as Opifer;
use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table(name="site")
 * @ORM\MappedSuperclass()
 */
class Site
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Opifer\Grid(listable=true)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     * @Assert\NotBlank()
     * @Opifer\Form(editable=true)
     * @Opifer\Grid(listable=true, type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Opifer\Form(editable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=128)
     * @Opifer\Form(editable=true)
     * @Opifer\Grid(listable=true, type="string")
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/",
     *     message="The domain should be a valid domain"
     * )
     */
    private $domain;

    /**
     * @var string
     *
     * @ORM\Column(name="cookie_domain", type="string", length=128, nullable=true)
     * @Opifer\Form(editable=true)
     */
    private $cookieDomain;

    /**
     * @var string
     *
     * @ORM\Column(name="default_locale", type="string", length=5)
     * @Opifer\Form(editable=true)
     * @Opifer\Grid(listable=true, type="string")
     * @Assert\NotBlank()
     */
    private $defaultLocale;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Site
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
     * Set description
     *
     * @param  string $description
     * @return Site
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
     * Set domain
     *
     * @param  string $domain
     * @return Site
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set cookieDomain
     *
     * @param  string $cookieDomain
     * @return Site
     */
    public function setCookieDomain($cookieDomain)
    {
        $this->cookieDomain = $cookieDomain;

        return $this;
    }

    /**
     * Get cookieDomain
     *
     * @return string
     */
    public function getCookieDomain()
    {
        return $this->cookieDomain;
    }

    /**
     * Set defaultLocale
     *
     * @param  string $defaultLocale
     * @return Site
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }

    /**
     * Get defaultLocale
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }
}
