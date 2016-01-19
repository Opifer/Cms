<?php

namespace Opifer\MediaBundle\Provider;

use Opifer\MediaBundle\Model\Media;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\MediaBundle\Model\MediaManagerInterface;
use Opifer\MediaBundle\Validator\Constraint\YoutubeUrl;

/**
 * Youtube Provider.
 */
class YoutubeProvider extends AbstractProvider
{
    /** @var MediaManagerInterface */
    protected $mediaManager;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var  string */
    private $apikey;

    /**
     * Constructor.
     *
     * @param MediaManagerInterface $mm
     * @param TranslatorInterface   $translator
     * @param string                $apikey
     */
    public function __construct(MediaManagerInterface $mm, TranslatorInterface $tr, $apikey)
    {
        $this->mediaManager = $mm;
        $this->translator = $tr;
        $this->apikey = $apikey;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->translator->trans('youtube.label');
    }

    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', 'text', [
                'data' => ($options['data']->getId()) ? 'https://www.youtube.com/watch?v='.$options['data']->getReference() : '',
                'label' => $this->translator->trans('youtube.reference.label'),
                'constraints' => [
                    new NotBlank(),
                    new Url(),
                    new YoutubeUrl(),
                ],
            ])
            ->add('thumb', 'mediapicker', [
                'multiple' => false,
                'property' => 'name',
                'class' => $this->mediaManager->getClass(),
            ])
            ->add('add '.$options['provider']->getLabel(), 'submit')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getThumb(MediaInterface $media)
    {
        return $media->getThumb()->getReference();
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(MediaInterface $media)
    {
        $this->preSave($media);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(MediaInterface $media)
    {
        $this->preSave($media);
    }

    /**
     * pre saving handler.
     *
     * @param MediaInterface $media
     */
    public function preSave(MediaInterface $media)
    {
        preg_match('/(?<=v(\=|\/))([-a-zA-Z0-9_]+)|(?<=youtu\.be\/)([-a-zA-Z0-9_]+)/', $media->getReference(), $matches);

        $media->setReference($matches[2]);

        if (!isset($media->old) || $media->old->getReference() !== $media->getReference()) {
            $this->updateMedadata($media);
        }
    }

    /**
     * Update metadata.
     *
     * @param MediaInterface $media
     * @param bool           $force
     */
    public function updateMedadata(MediaInterface $media)
    {
        try {
            $url = sprintf(
                'https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=snippet,contentDetails,statistics,status',
                $media->getReference(),
                $this->apikey
            );
            $metadata = $this->getMetadata($media, $url);
        } catch (\RuntimeException $e) {
            $media->setStatus(Media::STATUS_DISABLED);

            return;
        }

        $metadata = $metadata['items'][0];
        $metadata['contentDetails']['duration'] = $this->convertDuration($metadata['contentDetails']['duration']);

        $media->setName($metadata['snippet']['title']);

        $thumb = $this->saveThumbnail($media, $metadata['snippet']['thumbnails']['high']['url']);
        $media->setThumb($thumb);

        $media->setMetadata($metadata);
        $media->setContentType('video/x-flv');
    }

    /**
     * Transforms Youtube's time format to a more readable one.
     *
     * @param string $duration
     *
     * @return string
     */
    public function convertDuration($duration)
    {
        $duration = new \DateInterval($duration);

        return $duration->format('%H:%I:%S');
    }

    /**
     * @throws \RuntimeException
     *
     * @param MediaInterface $media
     * @param string         $url
     *
     * @return mixed
     */
    protected function getMetadata(MediaInterface $media, $url)
    {
        try {
            $metadata = file_get_contents($url);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Unable to retrieve the video information for :'.$url, null, $e);
        }

        $metadata = json_decode($metadata, true);

        if (!$metadata) {
            throw new \RuntimeException('Unable to decode the video information for :'.$url);
        }

        return $metadata;
    }

    /**
     * Save the thumbnail.
     *
     * @param MediaInterface $media The Youtube Object
     * @param string         $url
     *
     * @return MediaInterface The newly created image
     */
    public function saveThumbnail(MediaInterface $media, $url)
    {
        $thumb = $this->mediaManager->createMedia();

        $thumb
            ->setStatus(Media::STATUS_HASPARENT)
            ->setName($media->getName().'_thumb')
            ->setProvider('image')
        ;

        $filename = '/tmp/'.basename($url);
        $filesystem = new Filesystem();
        $filesystem->dumpFile($filename, file_get_contents($url));
        $thumb->temp = $filename;
        $thumb->setFile(new UploadedFile($filename, basename($url)));

        $this->mediaManager->save($thumb);

        return $thumb;
    }
}
