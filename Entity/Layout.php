<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Model\Layout as BaseLayout;

/**
 * Layout
 *
 * @ORM\MappedSuperclass()
 * @ORM\Table(name="layout")
 */
class Layout extends BaseLayout
{
}
