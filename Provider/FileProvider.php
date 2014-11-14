<?php

namespace Opifer\MediaBundle\Provider;

use Gaufrette\FileSystem;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

use Opifer\MediaBundle\Model\MediaInterface;

class FileProvider extends AbstractProvider
{
    /**
     * @var  Gaufrette\FileSystem
     */
    protected $filesystem;

    /**
     * @var  Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var  Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * Constructor
     *
     * @param FileSystem $filesystem
     */
    public function __construct(FileSystem $filesystem, TranslatorInterface $translator, RouterInterface $router)
    {
        $this->filesystem = $filesystem;
        $this->translator = $translator;
        $this->router = $router;
    }

    public function getLabel()
    {
        return $this->translator->trans('file.label');
    }

    /**
     * {@inheritdoc}
     */
    public function newView()
    {
        return 'OpiferMediaBundle:File:new.html.twig';
    }

    /**
     * Build the add file form
     *
     * @param Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                       $options
     *
     * @return void
     */
    public function buildCreateForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('files', 'dropzone', [
                'mapped' => false,
                'path' => $this->router->generate('opifer_api_media_upload'),
                'form_action' => $this->router->generate('opifer_media_media_updateall'),
                'label' => ''
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildEditForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => ucfirst($this->translator->trans('file.name.label'))
            ])
            ->add('Update', 'submit')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function prePersist(MediaInterface $media)
    {
        if ($media->getFile() === null) {
            return;
        }

        $filename = 'originalfilename';

        $media
            ->setReference($filename.'.'.$media->getFile()->guessExtension())
            ->setContentType($media->getFile()->getClientMimeType())
            ->setFilesize($media->getFile()->getSize())
            ->setMetadata('metadata')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function postPersist(MediaInterface $media)
    {
        $this->upload($media);
    }

    /**
     * {@inheritDoc}
     */
    public function postUpdate(MediaInterface $media)
    {
        $this->upload($media);
    }

    /**
     * {@inheritDoc}
     */
    public function postRemove(MediaInterface $media)
    {
        if ($this->filesystem->has($media->getReference())) {
            $this->filesystem->delete($media->getReference());
        }
    }

    /**
     * Upload a file
     *
     * @param MediaInterface $media
     *
     * @return void
     */
    public function upload(MediaInterface $media)
    {
        // the file property can be empty if the field is not required
        if (null === $media->getFile()) {
            return;
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
        $ext = '.' . $file->guessExtension();
        $basename = trim(str_replace('.'.$file->getClientOriginalExtension(), '', $file->getClientOriginalName()));
        $basename = str_replace(' ', '-', $basename);
        
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
            $id++;

            $basename = $basename . '-' . $id;
        }

        return $basename . $ext;
    }

    /**
     * Get Filesystem
     *
     * @return Gaufrette\FileSystem
     */
    public function getFileSystem()
    {
        return $this->filesystem;
    }
}
