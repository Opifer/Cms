<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\JumbotronBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class JumbotronBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class JumbotronBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->get('properties')
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id'],'required' => false])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes'],'required' => false])
            ->add('autoplay', ChoiceType::class, [
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ],
                'attr' => [
                    'help_text' => 'help.autoplay'
                ],
            ])
            ->add('loop', ChoiceType::class, [
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ],
                'attr' => [
                    'help_text' => 'help.loop'
                ],
            ])
        ;

        if ($this->config['styles']) {
            $builder->get('properties')
                ->add('styles', ChoiceType::class, [
                    'label' => 'label.styling',
                    'choices'  => $this->config['styles'],
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'attr' => ['help_text' => 'help.html_styles','tag' => 'styles'],
                ]);
        }

        if (isset($this->config['templates'])) {
            $builder->get('properties')
                ->add('template', ChoiceType::class, [
                    'label'       => 'label.template',
                    'placeholder' => 'placeholder.choice_optional',
                    'attr'        => ['help_text' => 'help.block_template','tag' => 'styles'],
                    'choices'     => $this->config['templates'],
                    'required'    => false,
                ]);
        }

        $builder->add(
            $builder->get('default')
                ->add('media', MediaPickerType::class, [
                    'required'  => false,
                    'multiple' => false,
                    'attr' => [
                        'label_col' => 12,
                        'widget_col' => 12,
                        'help_text' => 'help.jumbotron_media'
                    ],
                ])
                ->add('value', CKEditorType::class, [
                    'label' => 'label.rich_text',
                    'required' => false,
                    'attr' => [
                        'label_col' => 12,
                        'widget_col' => 12,
                        'help_text' => 'help.jumbotron_rich_text'
                    ]
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
        return new JumbotronBlock;
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
        $tool = new Tool('Jumbotron', 'jumbotron');

        $tool->setIcon('settings_overscan')
            ->setDescription('Large piece of content with bigger font and optional background image.');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'This creates a Jumbotron. Large piece of content with bigger font and optional background image.';
    }
}
