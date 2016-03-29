<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\Common\Collections\ArrayCollection;
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
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

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
     * @param EngineInterface $templating
     * @param array $config
     * @param MediaManager $mediaManager
     */
    public function __construct(EngineInterface $templating, array $config, MediaManager $mediaManager, Container $container)
    {
        parent::__construct($templating, $config);

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
    public function downloadMedia($filename)
    {
        $media = $this->mediaManager->getRepository()->findOneByReference($filename);
        $provider = $this->container->get('opifer.media.provider.pool')->getProvider($media->getProvider());
        
        $reference = $provider->getThumb($media);
        $mediaUrl = $provider->getUrl($media);
        
        $response = new Response();
        $response->headers->set('Content-type', 'application/octect-stream');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $reference));
        $response->setContent($mediaUrl);
        
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
            ->setDescription('Allows to download media items');

        return $tool;
    }
}
