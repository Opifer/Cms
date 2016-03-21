<?php

namespace Opifer\MediaBundle\File;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

class ImageTypeGuesser implements MimeTypeGuesserInterface
{
    /**
     * {@inheritDoc}
     */
    public function guess($path)
    {
//        if (!is_file($path)) {
//            throw new FileNotFoundException($path);
//        }
//
//        if (!is_readable($path)) {
//            throw new AccessDeniedException($path);
//        }

        $f = fopen($path, 'r');
        $line = fgets($f);
        fclose($f);

        if (strpos($line, '<svg') !== false) {
            return 'image/svg+xml';
        }

        return null;
    }
}
