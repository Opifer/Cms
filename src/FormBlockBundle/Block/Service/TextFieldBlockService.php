<?php

namespace Opifer\FormBlockBundle\Block\Service;

use Opifer\FormBlockBundle\Entity\TextFieldBlock;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form Block Service.
 */
class TextFieldBlockService extends FormFieldBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('properties')
            ->add('unit', TextType::class, [
                'required' => false,
                'attr' => [
                    'help_text' => 'An optional value unit (e.g. m2)',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new TextFieldBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Text Field', 'text_field');

        $tool->setIcon('input')
            ->setGroup('Form')
            ->setDescription('Include a text field');

        return $tool;
    }
}
