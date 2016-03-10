<?php

namespace Opifer\ContentBundle\Provider;

use Opifer\ContentBundle\Block\BlockOwnerInterface;

interface BlockProviderInterface
{
    /**
     * Returns an instance of an object of type BlockOwnerInterface
     *
     * @param $id
     *
     * @return BlockOwnerInterface
     */
    public function getBlockOwner($id);
}