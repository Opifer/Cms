<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\GalleryBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Opifer\MediaBundle\Model\MediaManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Gallery Block Service
 */
class GalleryBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var MediaManager */
    protected $mediaManager;

    /**
     * Constructor.
     *
     * @param BlockRenderer $blockRenderer
     * @param array $config
     * @param MediaManager $mediaManager
     */
    public function __construct(BlockRenderer $blockRenderer, array $config, MediaManager $mediaManager)
    {
        parent::__construct($blockRenderer, $config);

        $this->mediaManager = $mediaManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('default')
            ->add('value',  MediaPickerType::class, [
                'to_json' => true,
                'multiple' => true,
                'label'    => 'Media',
            ])
        ;

        $builder->get('properties')
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']])
        ;

        if (isset($this->config['templates'])) {
            $builder->get('styles')->add('template', ChoiceType::class, [
                'label'       => 'label.template',
                'placeholder' => 'placeholder.choice_optional',
                'attr'        => ['help_text' => 'help.block_template'],
                'choices'     => $this->config['templates'],
                'required'    => false,
            ]);
        }
    }

    /**
     * @param BlockInterface $block
     */
    public function load(BlockInterface $block)
    {
        $ids = json_decode($block->getValue());

        if (empty($ids) || ! count($ids)) {
            return;
        }

        $gallery = $this->mediaManager->getRepository()->findByIds($ids);

        uasort($gallery, function ($a, $b) use ($ids) {
            return (array_search($a->getId(), $ids) > array_search($b->getId(), $ids));
        });

        if ($gallery) {
            $block->setGallery(new ArrayCollection($gallery));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new GalleryBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Gallery', 'gallery');

        $tool->setIcon('view_module')
            ->setDescription('A collection of media thumbnails');

        return $tool;
    }
}
