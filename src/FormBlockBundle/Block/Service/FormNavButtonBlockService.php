<?php

namespace Opifer\FormBlockBundle\Block\Service;

use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\FormBlockBundle\Entity\FormNavButtonBlock;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form Navigation Button Block Service.
 */
class FormNavButtonBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('properties')
            ->add('direction', ChoiceType::class, [
                'choices' => [
                    'Next' => 'next',
                    'Previous' => 'previous',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new FormNavButtonBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Next/Previous Button', 'form_nav_button');

        $tool->setIcon('navigate_next')
            ->setGroup('Form')
            ->setDescription('Add a button to navigate between sections');

        return $tool;
    }
}
