<?php

namespace Opifer\CmsBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

class Domain
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     * 
     * @Assert\Regex(
     *     pattern="/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/",
     *     message="The domain should be a valid domain"
     * )
     * @JMS\Expose
     */
    protected $domain;

    /**
     * @var Site
     */
    protected $site;

    /**
     * @var bool
     */
    protected $default = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return Domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     * @return Domain
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->domain;
    }

    /**
     * @return bool
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param $default
     * @return Domain
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

}
