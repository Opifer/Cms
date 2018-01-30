<?php

namespace Opifer\MediaBundle\Provider;

use Gaufrette\Adapter\AwsS3;
use Gaufrette\FileSystem;
use Opifer\MediaBundle\Form\Type\DropzoneType;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\MediaBundle\Routing\UrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        if ($media->getFile() instanceof UploadedFile && !$media->getFile()->isValid()) {
            $this->handleError($media->getFile()->getError());
        }

        $adapter = $this->filesystem->getAdapter();

        if ($adapter instanceof AwsS3) {
            $adapter->setMetadata($media->getReference(), [
                'ContentType' => $media->getContentType(),
                'Cache-Control' => 'max-age=86400',
            ]);
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
     * @param string $error
     *
     * @throws FileException When an error occurred during the file upload process
     */
    public function handleError($error)
    {
        switch($error) {
            case UPLOAD_ERR_INI_SIZE:
                throw new FileException('The uploaded file exceeds the upload_max_filesize directive in php.ini');
            case UPLOAD_ERR_FORM_SIZE:
                throw new FileException('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
            case UPLOAD_ERR_PARTIAL:
                throw new FileException('The uploaded file was only partially uploaded');
            case UPLOAD_ERR_NO_TMP_DIR:
                throw new FileException('Missing a temporary folder');
            case UPLOAD_ERR_CANT_WRITE:
                throw new FileException('Failed to write file to disk');
            case UPLOAD_ERR_EXTENSION:
                throw new FileException('A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help');
            default:
                return;
        }
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
        return $this->getUrlByReference($media->getReference());
    }

    /**
     * @param string $reference
     *
     * @return string
     */
    public function getUrlByReference($reference)
    {
        return $this->urlGenerator->generate($reference);
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
        $ext = '.'.$file->getClientOriginalExtension();
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

        return strtolower($basename.$ext);
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
