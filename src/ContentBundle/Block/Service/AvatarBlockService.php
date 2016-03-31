<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\AvatarBlock;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Avatar Block Service
 */
class AvatarBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block'         => $block,
        ];

        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new AvatarBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Avatar', 'avatar');

        $tool->setIcon('account_box')
            ->setDescription('Shows logged user data or login/register button');

        return $tool;
    }
}
