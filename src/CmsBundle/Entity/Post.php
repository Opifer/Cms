<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Opifer\EavBundle\Model\EntityInterface;
use Opifer\FormBundle\Model\Post as BasePost;

/**
 * Post
 *
 * @JMS\ExclusionPolicy("all")
 * @GRID\Source(columns="id, submittedAt")
 */
class Post extends BasePost implements EntityInterface
{
    /**
     * @var int
     *
     * @JMS\Expose
     *
     * @GRID\Column(title="Id", size="10", type="number")
     */
    protected $id;
}
