<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ImageBlock;
use Opifer\ContentBundle\Form\Type\StylesType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

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
        $builder->get('default')
            ->add('media', MediaPickerType::class, [
                'required'  => false,
                'multiple' => false,
                'attr' => array('label_col' => 12, 'widget_col' => 12),
            ]);

        $builder->get('properties')
            ->add('styles', StylesType::class, [
                'choices'  => $this->config['styles'],
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
                        $explanation[] = $properties['size'][0].' X '. $properties['size'][1];
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

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Provides an image from the library in the right size.';
    }
}
