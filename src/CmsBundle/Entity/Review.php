<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\ORM\Mapping as ORM;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\ReviewBundle\Model\Review as BaseReview;

/**
 * @GRID\Source(columns="id, title, author")
 */
class Review extends BaseReview
{
    /**
     * @var int
     *
     * @GRID\Column(title="Id", size="10", type="number")
     */
    protected $id;

    /**
     * @var MediaInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\MediaBundle\Model\MediaInterface")
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $avatar;

    /**
     * @return MediaInterface
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param MediaInterface $avatar
     *
     * @return Review
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }
}
