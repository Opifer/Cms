<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\DownloadsBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\MediaBundle\Model\MediaManager;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Gaufrette\FileSystem;

/**
 * Video Block Service
 */
class DownloadsBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
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
    public function __construct(BlockRenderer $blockRenderer, array $config, MediaManager $mediaManager, Container $container)
    {
        parent::__construct($blockRenderer, $config);

        $this->container = $container;
        $this->mediaManager = $mediaManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('value', MediaPickerType::class, [
                    'to_json' => true,
                    'multiple' => true,
                    'label' => 'label.content'
                ])
        );
    }

    /**
     * Download media item.
     *
     * @param string $filename
     *
     * @return Response
    */
    public function downloadMediaAction($filename)
    {
        $media = $this->mediaManager->getRepository()->findOneByReference($filename);
        $provider = $this->container->get('opifer.media.provider.pool')->getProvider($media->getProvider());

        $mediaUrl = $provider->getUrl($media);

        $fileSystem = $provider->getFileSystem();
        $file = $fileSystem->read($media->getReference());
        
        $response = new Response();
        $response->headers->set('Content-type', $media->getContentType());
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', basename($mediaUrl)));
        $response->setContent($file);
        
        return $response;
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

        $items = $this->mediaManager->getRepository()->findByIds($ids);

        uasort($items, function ($a, $b) use ($ids) {
            return (array_search($a->getId(), $ids) > array_search($b->getId(), $ids));
        });

        if ($items) {
            $block->setItems(new ArrayCollection($items));
        }
    }    

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new DownloadsBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Downloads', 'downloads');

        $tool->setIcon('file_download')
            ->setDescription('List of media items available for download');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block)
    {
        return 'List of media items available for download';
    }
}
