<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\NavBarBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * NavBar Block Service
 */
class NavBarBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new NavBarBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Nav bar', 'navbar');

        $tool->setIcon('menu')
            ->setGroup('navigation')
            ->setDescription('A navigation bar');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block)
    {
        return 'A navigation bar';
    }
}
