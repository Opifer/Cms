<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Entity\LayoutBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Entity\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TemplateBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class TemplateBlockService extends LayoutBlockService
{
    protected $view = 'OpiferContentBundle:Block:template.html.twig';

    /**
     * {@inheritDoc}
     */
    public function getManageFormTypeName()
    {
        return 'template';
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Template';
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new Template;
    }
}