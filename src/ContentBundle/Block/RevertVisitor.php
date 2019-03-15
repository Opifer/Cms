<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Model\BlockInterface;

class RevertVisitor implements VisitorInterface
{
    /** @var integer */
    protected $rootVersion;

    /** @var BlockManager */
    protected $blockManager;


    /**
     * @param BlockManager $blockManager
     * @param integer      $rootVersion
     */
    public function __construct(BlockManager $blockManager, $rootVersion)
    {
        $this->blockManager = $blockManager;
        $this->rootVersion = $rootVersion;
    }

    /**
     * @param BlockInterface $block
     */
    public function visit(BlockInterface $block)
    {
        $this->blockManager->revertSingle($block, $this->rootVersion);
    }
}
