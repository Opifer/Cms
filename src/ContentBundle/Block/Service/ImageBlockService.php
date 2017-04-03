<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ImageBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ImageBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class ImageBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * All available filtersets with its configuration
     *
     * @var array
     */
    protected $filterSets;

    /**
     * Constructor.
     *
     * @param BlockRenderer $blockRenderer
     * @param array           $filterSets
     * @param array           $config
     */
    public function __construct(BlockRenderer $blockRenderer, array $filterSets, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->filterSets = $filterSets;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('media', MediaPickerType::class, [
                    'required'  => false,
                    'multiple' => false,
                    'attr' => array('label_col' => 12, 'widget_col' => 12),
                ])
        )->add(
            $builder->create('properties', FormType::class)
                ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
                ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']])
                ->add('filter', ChoiceType::class, [
                    'choices' => $this->getAvailableFilters(),
                    'choices_as_values' => true,
                    'attr' => ['help_text' => 'help.image_filter']
                ])
                ->add('enlarge', ChoiceType::class, [
                    'choices' => ['No' => false, 'Yes' => true],
                    'choices_as_values' => true,
                    'attr' => ['help_text' => 'help.image_enlarge']
                ])
                ->add('enlarge_filter', ChoiceType::class, [
                    'choices' => $this->getAvailableFilters(),
                    'choices_as_values' => true,
                    'required' => false,
                    'attr' => ['help_text' => 'help.image_enlarge_filter']
                ])
        );

        $builder->get('styles')
            ->add('styles', ChoiceType::class, [
                'label' => 'label.styling',
                'choices'  => $this->config['styles'],
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr' => ['help_text' => 'help.html_styles'],
            ]);
    }

    /**
     * Formats the available filters
     *
     * @return array
     */
    protected function getAvailableFilters()
    {
        $filters = [];

        foreach ($this->config['allowed_filters'] as $key) {
            if (!isset($this->filterSets[$key])) {
                continue;
            }

            $set = $this->filterSets[$key];

            $explanation = [];
            foreach ($set['filters'] as $filter => $properties) {
                switch ($filter) {
                    case 'thumbnail':
                        $explanation[] = $properties['size'][0].' X '. $properties['size'][0];
                        break;
                    case 'relative_resize':
                        $heighten = (isset($properties['heighten'])) ? $properties['heighten'] : '~';
                        $widen = (isset($properties['widen'])) ? $properties['widen'] : '~';
                        $explanation[] = $widen.' X '.$heighten;
                        break;
                }
            }
            $filters[$key . ' ('.implode(', ', $explanation).')'] = $key;
        }

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new ImageBlock;
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Image', 'image');

        $tool->setIcon('image')
            ->setDescription('Provides an image from the library in the right size.');

        return $tool;
    }
}
