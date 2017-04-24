<?php

namespace Opifer\MediaBundle\Provider;

use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Opifer\MediaBundle\Model\Media;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\MediaBundle\Model\MediaManagerInterface;
use Opifer\MediaBundle\Validator\Constraint\VimeoUrl;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

/**
 * Vimeo Provider.
 */
class VimeoProvider extends AbstractProvider
{
    const WATCH_URL = 'https://vimeo.com';

    /** @var MediaManagerInterface */
    protected $mediaManager;

    /** @var TranslatorInterface */
    protected $translator;

    protected $vimeoData;

    /**
     * Constructor.
     *
     * @param MediaManagerInterface $mm
     * @param TranslatorInterface   $tr
     */
    public function __construct(MediaManagerInterface $mm, TranslatorInterface $tr)
    {
        $this->mediaManager = $mm;
        $this->translator = $tr;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->translator->trans('vimeo.label');
    }

    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', TextType::class, [

                'label' => $this->translator->trans('vimeo.reference.label'),
                'constraints' => [
                    new NotBlank(),
                    new Url(),
                    new VimeoUrl(),
                ],
            ])
            ->add('thumb', MediaPickerType::class, [
                'multiple' => false,
                'property' => 'name',
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
     */
    public function preSave(MediaInterface $media)
    {
        $vimeoData = $this->getVimeoData($media);

        $media->setReference($vimeoData->video_id);

        $media->setName($vimeoData->title);

        $thumb = $this->saveThumbnail($media, $vimeoData->thumbnail_url);
        $media->setThumb($thumb);

        $media->setMetadata($vimeoData);

        $media->setContentType('video/x-flv');
    }

    /**
     * Transforms Vimeo's time format to a more readable one.
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
     * @param MediaInterface $media The Vimeo Object
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

    /**
     * Get the full url to the original video.
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function getUrl(MediaInterface $media)
    {
        return self::WATCH_URL.'/'.$media->getReference();
    }

    public function getVimeoData(MediaInterface $media)
    {
        $oembed_endpoint = 'http://vimeo.com/api/oembed';

        $xml_url = $oembed_endpoint . '.xml?url=' . rawurlencode($media->getReference());

        $curl = curl_init($xml_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        $vimeoResult = curl_exec($curl);
        curl_close($curl);

        return simplexml_load_string($vimeoResult);
    }
}
