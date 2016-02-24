<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\ContentTool;
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
        $gallery = $this->mediaManager->getRepository()->findByIds(json_decode($block->getValue()));
        if ($gallery) {
            $block->setGallery($gallery);
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
        $tool = new ContentTool('Gallery', 'OpiferContentBundle:GalleryBlock');

        $tool->setIcon('view_module')
            ->setDescription('A collection of media thumbnails');

        return $tool;
    }
}
