<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ButtonBlock;
use Opifer\ContentBundle\Form\Type\StylesType;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Button Block Service
 */
class ButtonBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->get('properties')
            ->add('url', TextType::class, [
                'label' => 'label.url',
                'attr' => ['help_text' => 'help.button_url']
            ])
            ->add('target', ChoiceType::class, [
                'label' => 'label.target',
                'choices' => ['_blank' => '_blank', '_self' => '_self'],
                'required' => false,
                'attr' => ['help_text' => 'help.button_target']
            ])
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id'],'required' => false])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes'],'required' => false]);


        if ($this->config['styles']) {
            $builder->get('properties')
                ->add('styles', StylesType::class, [
                    'choices'  => $this->config['styles'],
                ]);
        }

        $builder->add(
            $builder->get('default')
                ->add('value', TextType::class, [
                    'label' => 'label.label',
                    'attr' => [
                        'help_text' => 'help.button_label'
                    ]
                ])
                ->add('name', TextType::class, [
                    'attr' => [
                        'help_text' => 'help.block_name'
                    ],
                    'required' => false
                ])
        )->add(
            $propertiesForm
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new ButtonBlock;
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->config['styles'];
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Button link', 'button');

        $tool->setIcon('link')
            ->setDescription('Creates a link to a (external) page or content');

        return $tool;
    }
}
