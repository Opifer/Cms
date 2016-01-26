<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Model\ContentType as BaseContentType;

/**
 * Content.
 *
 * @ORM\Entity()
 * @ORM\Table(name="content_type")
 * @GRID\Source(columns="id, name")
 */
class ContentType extends BaseContentType
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @GRID\Column(title="Id", size="10", type="number")
     */
    protected $id;
}
