<?php

namespace Opifer\MediaBundle\Provider;

use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Opifer\MediaBundle\Model\Media;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\MediaBundle\Model\MediaManagerInterface;
use Opifer\MediaBundle\Validator\Constraint\YoutubeUrl;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

/**
 * Youtube Provider.
 */
class YoutubeProvider extends AbstractProvider
{
    const WATCH_URL = 'https://www.youtube.com/watch';

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
     * @param TranslatorInterface   $tr
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
            ->add('reference', TextType::class, [
                'data' => ($options['data']->getId()) ? self::WATCH_URL.'?v='.$options['data']->getReference() : '',
                'label' => $this->translator->trans('youtube.reference.label'),
                'constraints' => [
                    new NotBlank(),
                    new Url(),
                    new YoutubeUrl(),
                ],
            ])
            ->add('name', TextType::class, [
                'label' => $this->translator->trans('youtube.name'),
                'required' => false,
                'attr' => [
                    'help_text' => $this->translator->trans('youtube.helper'),
                ]
            ])
            ->add('thumb', MediaPickerType::class, [
                'multiple' => false,
                'choice_label' => 'name',
            ])
            ->add('add '.$options['provider']->getLabel(), SubmitType::class)
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
     * @throws \Exception
     */
    public function preSave(MediaInterface $media)
    {
        preg_match('/(?<=v(\=|\/))([-a-zA-Z0-9_]+)|(?<=youtu\.be\/)([-a-zA-Z0-9_]+)/', $media->getReference(), $matches);

        if(isset($matches[2])) {
            $media->setReference($matches[2]);
        }

        //Check if the reference already exists
        if (!$media->getId() && $referenceMedia = $this->mediaManager->getRepository()->findOneBy(['reference' => $media->getReference()])) {
            throw new \Exception(sprintf('Video with reference: %s already exists for under the name: %s', $media->getReference(), $referenceMedia->getName()));
        }

        if (!$media->getId() || ($media->old && $media->old->getReference() !== $media->getReference())) {
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

        if (!$media->getName()) {
            $media->setName($metadata['snippet']['title']);
        }

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

        $filename = '/tmp/'.md5(date('Ymd H:i:s')).'-'.basename($url);
        $filesystem = new Filesystem();
        $filesystem->dumpFile($filename, file_get_contents($url));
        $thumb->temp = $filename;
        $thumb->setFile(new UploadedFile($filename, md5(date('Ymd H:i:s')).'-'.basename($url)));

        $this->mediaManager->save($thumb);

        return $thumb;
    }

    /**
     * Get the full url to the original video.
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function getUrl(MediaInterface $media)
    {
        $metadata = $media->getMetaData();

        return sprintf('%s?v=%s', self::WATCH_URL, $metadata['id']);
    }
}
