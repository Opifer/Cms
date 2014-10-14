<?php

namespace Opifer\MediaBundle\Provider;

use Opifer\MediaBundle\Model\MediaInterface;

/**
 * Extends FileProvider to add image-specific functionality
 */
class ImageProvider extends FileProvider implements ProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return $this->translator->trans('image.label');
    }

    /**
     * {@inheritDoc}
     */
    public function indexView()
    {
        return 'OpiferMediaBundle:Image:single.html.twig';
    }

    /**
     * {@inheritDoc}
     */
    public function prePersist(MediaInterface $media)
    {
        if ($media->getFile() === null) {
            return;
        }

        $file = $media->getFile();
        $filename = $this->createUniqueFileName($file);

        // The status might have been set before. For example when the current
        // image is used as a thumbnail for another media item.
        if (!$media->getStatus()) {
            $media->setStatus(self::ENABLED);
        }

        $size = getimagesize($file);

        if (!$media->getName()) {
            $media->setName($filename);
        }

        $media
            ->setReference($filename)
            ->setContentType($file->getClientMimeType())
            ->setFilesize($file->getSize())
            ->setMetadata(['width' => $size[0], 'height' => $size[1]])
        ;
    }
}
