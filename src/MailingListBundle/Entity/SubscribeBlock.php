<?php

namespace Opifer\MailingListBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\ContentBundle\Entity\Block;

/**
 * Subscribe Block
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