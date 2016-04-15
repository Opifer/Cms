<?php

namespace Opifer\MailingListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Subscribe Block.
 *
 * @ORM\Entity
 */
class SubscribeBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'subscribe';
    }
}
