<?php

namespace Opifer\FormBlockBundle\Block\Service;

use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;
use Opifer\FormBlockBundle\Entity\ChoiceFieldBlock;
use Opifer\FormBlockBundle\Form\Type\KeyValueType;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Choice Field Block Service.
 */
class ChoiceFieldBlockService extends FormFieldBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->get('properties');

        $propertiesForm
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Buttons' => 'button',
                    'Radio Buttons' => 'radiobutton',
                    'Select field' => 'select',
                ],
            ])
            ->add('options', BootstrapCollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'entry_type' => KeyValueType::class,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new ChoiceFieldBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Choice Field', 'choice_field');

        $tool->setIcon('input')
            ->setGroup('Form')
            ->setDescription('Include a choice field');

        return $tool;
    }
}
