<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\AlertBlock;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Alert Block Service.
 */
class AlertBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add('value', TextareaType::class, [
            'label' => 'Message',
            'attr' => [
                'help_text' => 'Show an alert message'
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new AlertBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Alert', 'alert');

        $tool->setIcon('notifications')
            ->setDescription('Add an alert message');

        return $tool;
    }
}
