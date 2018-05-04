<?php

namespace Opifer\MediaBundle\File;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

class ImageTypeGuesser implements MimeTypeGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function guess($path)
    {
        $f = fopen($path, 'r');
        $line = fgets($f);
        fclose($f);

        if (strpos($line, '<svg') !== false) {
            return 'image/svg+xml';
        }

        return null;
    }
}
