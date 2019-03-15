<?php

namespace Opifer\ContentBundle\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\ContentBundle\Model\BlockInterface;

class SortVisitor implements VisitorInterface
{
    /**
     * @param BlockInterface $block
     */
    public function visit(BlockInterface $block)
    {
        if ($block instanceof BlockContainerInterface) {
            $iterator = $block->getChildren()->getIterator();

            $iterator->uasort(function ($a, $b) {
                return ($a->getSort() < $b->getSort()) ? -1 : 1;
            });

            $block->setChildren(new ArrayCollection(iterator_to_array($iterator)));
        }
    }
}
