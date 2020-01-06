<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ContainerBlock;
use Opifer\ContentBundle\Form\Type\StylesType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Form\Type\BoxModelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Container block service
 */
class ContainerBlockService extends AbstractBlockService implements LayoutBlockServiceInterface, BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('default')
            ->add('name', TextType::class, [
                'label' => 'label.name',
                'attr' => [
                    'help_text' => 'help.block_name',
                    'tag' => 'settings'
                ]
                ,'required' => false
            ]);

        $builder->get('properties')
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id'],'required' => false])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes'],'required' => false]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            if (count($this->config['styles'])) {
                $form->get('properties')
                    ->add('styles', StylesType::class, [
                        'choices'  => $this->config['styles'],
                    ]);
            }

            $form->get('properties')
                ->add('padding', BoxModelType::class, [
                    'type' => 'padding',
                    'attr' => [
                        'help_text' => 'Spacing inside',
                        'tag' => 'styles',
                    ],
                    'required' => false
                ])
                ->add('margin', BoxModelType::class, [
                    'type' => 'margin',
                    'attr' => [
                        'help_text' => 'Spacing outside',
                        'tag' => 'styles',
                    ],
                    'required' => false
                ])
                ->add('container_size', ChoiceType::class, [
                    'label' => 'label.container_sizing',
                    'choices' => ['fluid' => 'label.container_fluid', '' => 'label.container_fixed', 'smooth' => 'label.container_smooth'],
                    'attr' => [
                        'help_text' => 'help.container_sizing',
                        'tag' => 'styles'
                    ],
                ])
            ;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getManageFormTypeName()
    {
        return 'layout';
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new ContainerBlock;
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool($this->getName(), 'container');

        $tool
            ->setIcon('crop_free')
            ->setGroup(Tool::GROUP_LAYOUT)
            ->setDescription('Container element to hold columns or other blocks in');

        return $tool;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaceholders(BlockInterface $block = null)
    {
        return [0 => 'container'];
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Container element to hold columns or other blocks in';
    }
}
