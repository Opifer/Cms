<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\JavaScriptBlock;
use Opifer\ContentBundle\Form\Type\CodeMirrorType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

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
        $builder->add(
            $builder->create('default', FormType::class, ['inherit_data' => true])
                ->add('value', CodeMirrorType::class, ['label' => 'label.code', 'parameters' => ['mode' => 'css'], 'attr' => ['label_col' => 12, 'widget_col' => 12, 'help_text' => 'help.javascript_code']])
        );
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
    public function getTool()
    {
        $tool = new Tool('JavaScript', 'javascript');

        $tool->setIcon('code')
            ->setDescription('Include custom JavaScript code block');

        return $tool;
    }
}
