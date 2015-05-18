<?php

namespace Opifer\RedirectBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Redirect
 *
 * @ORM\MappedSuperclass
 */
class Redirect
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="origin", type="string", length=255)
     */
    protected $origin;

    /**
     * @var string
     *
     * @ORM\Column(name="target", type="string", length=255)
     */
    protected $target;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permanent", type="boolean")
     */
    protected $permanent;


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
     * Set origin
     *
     * @param string $origin
     *
     * @return Redirect
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set target
     *
     * @param string $target
     *
     * @return Redirect
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set permanent
     *
     * @param boolean $permanent
     *
     * @return Redirect
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;

        return $this;
    }

    /**
     * Is permanent
     *
     * @return boolean
     */
    public function isPermanent()
    {
        return $this->permanent;
    }
}

