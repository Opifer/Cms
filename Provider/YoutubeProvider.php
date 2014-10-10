<?php

namespace Opifer\MediaBundle\Provider;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Translation\LoggingTranslator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

use Opifer\MediaBundle\Entity\Media;
use Opifer\MediaBundle\Validator\Constraint\YoutubeUrl;

/**
 * Youtube Provider
 */
class YoutubeProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @var  Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var  Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    /**
     * @var  string
     */
    private $apikey;

    /**
     * Constructor
     *
     * @param Doctrine\ORM\EntityManager                            $em
     * @param Symfony\Bundle\FrameworkBundle\Translation\Translator $translator
     * @param string                                                $apikey
     */
    public function __construct(EntityManager $em, LoggingTranslator $translator, $apikey)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->apikey = $apikey;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return $this->translator->trans('youtube.label');
    }

    /**
     * {@inheritDoc}
     */
    public function indexView()
    {
        return 'OpiferMediaBundle:Youtube:single.html.twig';
    }

    /**
     * {@inheritDoc}
     */
    public function buildCreateForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', 'text', [
                'data' => ($options['data']->getId()) ? 'https://www.youtube.com/watch?v=' . $options['data']->getReference() : '',
                'label' => $this->translator->trans('youtube.reference.label'),
                'constraints' => [
                    new NotBlank(),
                    new Url(),
                    new YoutubeUrl()
                ]
            ])
            ->add('tags', 'tags', [
                'tagfield' => [],
                'autocomplete' => 'dynamic', // default
                'attr' => ['help_text' => $this->translator->trans('tag.help_text')]
            ])
            ->add('thumb', 'mediapicker', [
                'multiple' => false,
                'property' => 'name',
                'class' => 'OpiferMediaBundle:Media'
            ])
            ->add('add ' . $options['provider']->getLabel(), 'submit')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function editView()
    {
        return 'OpiferMediaBundle:Youtube:edit.html.twig';
    }

    /**
     * {@inheritDoc}
     */
    public function prePersist(Media $media)
    {
        $this->preSave($media);
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate(Media $media)
    {
        $this->preSave($media);
    }

    /**
     * pre saving handler
     *
     * @param Media $media
     *
     * @return void
     */
    public function preSave(Media $media)
    {
        preg_match('/(?<=v(\=|\/))([-a-zA-Z0-9_]+)|(?<=youtu\.be\/)([-a-zA-Z0-9_]+)/', $media->getReference(), $matches);

        $media
            ->setReference($matches[2])
            ->setStatus(self::ENABLED)
        ;

        if (!isset($media->old) || $media->old->getReference() !== $media->getReference()) {
            $this->updateMedadata($media);
        }
    }

    /**
     * Update metadata
     *
     * @param Media   $media
     * @param boolean $force
     *
     * @return void
     */
    public function updateMedadata(Media $media)
    {
        try {
            //$url = sprintf('http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=%s&format=json', $media->getReference());
            // BROWSER KEY; AIzaSyDpsolmAI0-ZmJqghJaBH5j16ifIzKnN9Q
            // SERVER KEY;  AIzaSyD_6br-YyFPNNUxucrMjQWphJJDpyv_4J4
            $url = sprintf(
                'https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=snippet,contentDetails,statistics,status',
                $media->getReference(),
                $this->apikey
            );
            $metadata = $this->getMetadata($media, $url);
        } catch (\RuntimeException $e) {
            $media->setStatus(self::DISABLED);

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
     * Transforms Youtube's time format to a more readable one
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
     * @param \Opifer\MediaBundle\Entity\Media $media
     * @param string                           $url
     *
     * @return mixed
     */
    protected function getMetadata(Media $media, $url)
    {
        try {
            $metadata = file_get_contents($url);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Unable to retrieve the video information for :' . $url, null, $e);
        }

        $metadata = json_decode($metadata, true);

        if (!$metadata)
            throw new \RuntimeException('Unable to decode the video information for :' . $url);

        return $metadata;
    }

    /**
     * Save the thumbnail
     *
     * @param Media  $media The Youtube Object
     * @param string $url
     *
     * @return Media The newly created image
     */
    public function saveThumbnail(Media $media, $url)
    {
        $thumb = new Media();

        $thumb
            ->setStatus(self::HASPARENT)
            ->setName($media->getName() . '_thumb')
            ->setProvider('image')
        ;

        $filename = '/tmp/' . basename($url);
        $filesystem = new Filesystem();
        $filesystem->dumpFile($filename, file_get_contents($url));
        $thumb->temp = $filename;
        $thumb->setFile(new UploadedFile($filename, basename($url)));

        $this->em->persist($thumb);

        return $thumb;
    }
}
