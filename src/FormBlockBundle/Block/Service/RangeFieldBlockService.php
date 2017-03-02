<?php

namespace Opifer\FormBlockBundle\Block\Service;

use Opifer\FormBlockBundle\Entity\RangeFieldBlock;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form Block Service.
 */
class RangeFieldBlockService extends FormFieldBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->get('properties');

        $propertiesForm
            ->add('from', NumberType::class, [
                'label'     => 'label.from',
                'required' => true,
                'empty_data' => 1,
                'attr' => [
                    'help_text' => 'help.range_from',
                ],
            ])
            ->add('to', NumberType::class, [
                'label'     => 'label.to',
                'required' => true,
                'empty_data' => 10,
                'attr' => [
                    'help_text' => 'help.range_to',
                ],
            ])
            ->add('stepSize', NumberType::class, [
                'label'     => 'label.step_size',
                'required' => true,
                'empty_data' => 1,
                'attr' => [
                    'help_text' => 'help.range_step_size',
                ],
            ])
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
        return new RangeFieldBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Range Field', 'range_field');

        $tool->setIcon('input')
            ->setGroup('Form')
            ->setDescription('Include a range slider');

        return $tool;
    }
}
