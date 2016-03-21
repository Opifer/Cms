<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\GalleryBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Opifer\MediaBundle\Model\MediaManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
     * @param EngineInterface $templating
     * @param array $config
     * @param MediaManager $mediaManager
     */
    public function __construct(EngineInterface $templating, array $config, MediaManager $mediaManager)
    {
        parent::__construct($templating, $config);

        $this->mediaManager = $mediaManager;
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
                ->add('value',  MediaPickerType::class, [
                    'to_json' => true,
                    'multiple' => true,
                    'label'    => 'label.content',
                ])
        );
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
    public function getTool()
    {
        $tool = new Tool('Gallery', 'gallery');

        $tool->setIcon('view_module')
            ->setDescription('A collection of media thumbnails');

        return $tool;
    }
}
