<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\AlertBlock;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Form\Type\StylesType;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

        $builder->add(
            $builder->create('default', FormType::Class, ['inherit_data' => true])
                ->add('value', TextareaType::class, [
                    'label' => 'Message',
                    'attr' => [
                        'help_text' => 'help.alert_message'
                    ]
                ])
        );

        $builder->get('properties')
            ->add('id', TextType::class, [
                'attr' => [
                    'help_text' => 'help.html_id',
                    'tag' => 'settings'
                ],
                'required' => false
            ])
            ->add('extra_classes', TextType::class, [
                'attr' => [
                    'help_text' => 'help.extra_classes',
                    'tag' => 'settings'
                ],
                'required' => false
            ]);


        $builder->get('properties')
            ->add('styles', StylesType::class, [
                'choices'  => $this->config['styles'],
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

    /**
     * @param BlockInterface $block
     * @return mixed
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Add an alert message';
    }
}
