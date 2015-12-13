<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\RedirectBundle\Model\Redirect as BaseRedirect;

/**
 * @ORM\Entity()
 * @ORM\Table(name="redirect")
 */
class Redirect extends BaseRedirect
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
}
