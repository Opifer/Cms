<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\JavaScriptBlock;
use Opifer\ContentBundle\Form\Type\CodeMirrorType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * JavaScript Block Service
 */
class JavaScriptBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->get('default')
            ->add('value', CodeMirrorType::class, [
                'label' => 'label.code',
                'parameters' => [
                    'mode' => 'css'
                ],
                'attr' => [
                    'label_col' => 12,
                    'widget_col' => 12,
                    'help_text' => 'help.javascript_code'
                ],
                'required' => false
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new JavaScriptBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('JavaScript', 'javascript');

        $tool->setIcon('code')
            ->setDescription('Include custom JavaScript code block');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Include custom JavaScript code block';
    }
}
