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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * List Block Service.
 */
class ListBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    /**
     * Constructor
     *
     * @param BlockRenderer           $blockRenderer
     * @param ContentManagerInterface $contentManager
     * @param array                   $config
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

        $builder->get('properties')
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']]);

        // Default panel
        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('name', TextType::class, ['label' => 'label.name', 'attr' => ['help_text' => 'help.block_name', 'tag' => 'settings']])
                ->add('title',  TextType::class, [
                    'label' => 'label.display_name',
                    'attr' => ['help_text' => 'help.block_display_name', 'tag' => 'settings']
                ])
                ->add('value',  ContentListPickerType::class, [
                    'label' => 'label.content',
                ])
        );

        if ($this->config['templates'] && count($this->config['templates'])) {
            $builder->get('properties')
                ->add('template', ChoiceType::class, [
                    'label' => 'label.template',
                    'placeholder' => 'placeholder.choice_optional',
                    'attr' => ['help_text' => 'help.template','tag' => 'styles'],
                    'choices' => $this->config['templates'],
                    'required' => false,
            ]);
        }

        if ($this->config['styles']) {
            $builder->get('properties')->add('styles', ChoiceType::class, [
                'label' => 'label.styling',
                'choices' => $this->config['styles'],
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr' => ['help_text' => 'help.html_styles', 'tag' => 'styles'],
            ]);
        }
    }

    /**
     * We load the collection on the Execute instead of the Load method to avoid loading the collection
     * on API serialisation.
     *
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, Response $response = null, array $parameters = [])
    {
        $collection = $this->contentManager->getRepository()->findOrderedByIds(json_decode($block->getValue()));

        if ($collection) {
            $block->setCollection($collection);
        }

        return parent::execute($block, $response, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new ListBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('List', 'list');

        $tool->setIcon('list')
            ->setDescription('Adds references to a collection of content items');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Adds references to a collection of content items';
    }
}
