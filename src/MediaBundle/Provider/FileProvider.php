<?php

namespace Opifer\MediaBundle\Provider;

use Gaufrette\Adapter\AwsS3;
use Gaufrette\FileSystem;
use Opifer\MediaBundle\Form\Type\DropzoneType;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\MediaBundle\Routing\UrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FileProvider extends AbstractProvider
{
    /** @var FileSystem */
    protected $filesystem;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var RouterInterface */
    protected $router;

    /** @var UrlGenerator  */
    protected $urlGenerator;

    /**
     * @param FileSystem          $filesystem
     * @param TranslatorInterface $translator
     * @param RouterInterface     $router
     * @param UrlGenerator        $urlGenerator
     */
    public function __construct(FileSystem $filesystem, TranslatorInterface $translator, RouterInterface $router, UrlGenerator $urlGenerator)
    {
        $this->filesystem = $filesystem;
        $this->translator = $translator;
        $this->router = $router;
        $this->urlGenerator = $urlGenerator;
    }

    public function getLabel()
    {
        return $this->translator->trans('file.label');
    }

    /**
     * Build the add file form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildCreateForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('files', DropzoneType::class, [
                'mapped' => false,
                'path' => $this->router->generate('opifer_api_media_upload'),
                'form_action' => $this->router->generate('opifer_media_media_updateall'),
                'label' => '',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'file.name.label',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function postLoad(MediaInterface $media)
    {
        $media->setOriginal($this->getUrl($media));
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(MediaInterface $media)
    {
        if ($media->getFile() === null) {
            return;
        }

        $file = $media->getFile();
        $filename = $this->createUniqueFileName($file);

        if (!$media->getName()) {
            $media->setName($filename);
        }

        $media
            ->setReference($filename)
            ->setContentType($media->getFile()->getClientMimeType())
            ->setFilesize($media->getFile()->getSize())
            ->setMetadata('metadata')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(MediaInterface $media)
    {
        $this->upload($media);
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(MediaInterface $media)
    {
        $this->upload($media);
    }

    /**
     * {@inheritdoc}
     */
    public function postRemove(MediaInterface $media)
    {
        if ($this->filesystem->has($media->getReference())) {
            $this->filesystem->delete($media->getReference());
        }
    }

    /**
     * Upload a file.
     *
     * @param MediaInterface $media
     */
    public function upload(MediaInterface $media)
    {
        // the file property can be empty if the field is not required
        if (null === $media->getFile()) {
            return;
        }

        $adapter = $this->filesystem->getAdapter();

        if ($adapter instanceof AwsS3) {
            $adapter->setMetadata($media->getReference(), ['ContentType' => $media->getContentType()]);
        }

        $this->filesystem->write($media->getReference(), file_get_contents($media->getFile()));

        if (isset($media->temp)) {
            // delete the old image
            unlink($media->temp);
            // clear the temp image path
            $media->temp = null;
        }

        // clean up the file property as you won't need it anymore
        $media->setFile(null);
    }

    /**
     * Get the full url to the original file.
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function getUrl(MediaInterface $media)
    {
        return $this->urlGenerator->generate($media->getReference());
    }

    /**
     * Create a unique filename based on the original filename.
     *
     * This checks the filesystem for other files that start with the original base name.
     * It does not check the database to avoid overwriting files that are not persisted
     * to the database for whatever reason.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return string
     */
    public function createUniqueFileName($file)
    {
        $ext = '.'.$file->guessExtension();
        $basename = trim(str_replace('.'.$file->getClientOriginalExtension(), '', $file->getClientOriginalName()));
        $basename = str_replace(' ', '-', $basename);
        $basename = strtolower($basename);

        $existing = $this->filesystem->listKeys($basename);
        if (isset($existing['keys'])) {
            $existing = $existing['keys'];
        }

        if (count($existing)) {
            $ids = [1];
            foreach ($existing as $neighbor) {
                $neighbor = str_replace($ext, '', $neighbor);
                if (preg_match('/(\d+)$/', $neighbor, $matches)) {
                    $ids[] = intval($matches[1]);
                }
            }

            rsort($ids);
            $id = reset($ids);
            ++$id;

            $basename = $basename.'-'.$id;
        }

        return $basename.$ext;
    }

    /**
     * Get Filesystem.
     *
     * @return FileSystem
     */
    public function getFileSystem()
    {
        return $this->filesystem;
    }
}
