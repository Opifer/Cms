<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\CssBlock;
use Opifer\ContentBundle\Form\Type\CodeMirrorType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * CSS Block Service
 */
class CssBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->get('default')
                ->add('value', CodeMirrorType::class, [
                    'label' => 'label.code',
                    'parameters' => [
                        'mode' => 'css'
                    ],
                    'attr' => [
                        'label_col' => 12,
                        'widget_col' => 12,
                        'help_text' => 'help.css_code'
                    ],
                    'required' => false
                ])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new CssBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('CSS', 'css');

        $tool->setIcon('style')
            ->setDescription('Implement custom CSS styles to use');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Implement custom CSS styles to use';
    }
}
