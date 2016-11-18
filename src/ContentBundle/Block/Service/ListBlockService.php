<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\ORM\EntityManager;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ListBlock;
use Opifer\ContentBundle\Form\Type\ContentListPickerType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * List Block Service
 */
class ListBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    /**
     * @param BlockRenderer $blockRenderer
     * @param EntityManager   $em
     * @param array           $config
     */
    public function __construct(BlockRenderer $blockRenderer, ContentManagerInterface $contentManager, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->create('properties', FormType::class)
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']]);

        // Default panel
        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('name', TextType::class)
                ->add('title',  'text', [
                    'label' => 'label.title',
                ])
                ->add('value',  ContentListPickerType::class, [
                    'label'    => 'label.content',
                ])
        )->add($propertiesForm);

        if ($this->config['templates'] && count($this->config['templates'])) {
            $propertiesForm->add('template', ChoiceType::class, [
                'label'       => 'label.template',
                'placeholder' => 'placeholder.choice_optional',
                'attr'        => ['help_text' => 'help.block_template'],
                'choices'     => $this->config['templates'],
                'required'    => false,
            ]);
        }

        if ($this->config['styles']) {
            $propertiesForm->add('styles', ChoiceType::class, [
                'label' => 'label.styling',
                'choices'  => $this->config['styles'],
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr' => ['help_text' => 'help.html_styles'],
            ]);
        }
        
        $propertiesForm->add('displayType', ChoiceType::class, [
                'label' => 'label.list_display_type',
                'choices'  => [
                    'list' => 'List',
                    'cards' => 'Cards',
                    'table' => 'Table',
                ],
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'attr' => ['help_text' => 'help.list_display_type'],
            ])
            ->add('displaySize', ChoiceType::class, [
                'label' => 'label.list_display_size',
                'choices'  => [
                    null => 'Default', 
                    'sm' => 'Small',
                    'md' => 'Medium',
                    'lg' => 'Large',
                ],
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'attr' => ['help_text' => 'help.list_display_size'],
            ])
            ->add('imageRatio', ChoiceType::class, [
                'label' => 'label.list_image_ratio',
                'choices'  => [
                    null => 'No image', 
                    '11' => '1:1',
                    '43' => '4:3',
                    '34' => '3:4 (portrait)',
                    '32' => '3:2',
                    '23' => '2:3 (portrait)',
                    '169'=> '16:9',
                    '916'=> '9:16 (portrait)',
                ],
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'attr' => ['help_text' => 'help.list_image_ratio']
            ]);
    }

    /**
     * @param BlockInterface $block
     */
    public function load(BlockInterface $block)
    {
        $collection = $this->contentManager->getRepository()->findOrderedByIds(json_decode($block->getValue()));

        if ($collection) {
            $block->setCollection($collection);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new ListBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('List', 'list');

        $tool->setIcon('list')
            ->setDescription('Adds references to a collection of content items');

        return $tool;
    }
}
