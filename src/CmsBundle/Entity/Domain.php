<?php

namespace Opifer\CmsBundle\Entity;

class Domain
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var Site
     */
    protected $site;

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
}
