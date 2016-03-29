<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\RedirectBundle\Model\Redirect as BaseRedirect;

/**
 */
class Redirect extends BaseRedirect
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $origin;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var bool
     */
    protected $permanent;
}
