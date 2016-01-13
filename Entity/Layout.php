<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Model\Layout as BaseLayout;

/**
 * Layout.
 *
 * @ORM\Entity
 * @ORM\Table(name="layout")
 * @GRID\Source(columns="id, name, filename, action")
 */
class Layout extends BaseLayout
{
}
