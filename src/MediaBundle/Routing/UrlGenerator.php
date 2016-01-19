<?php

namespace Opifer\MediaBundle\Routing;

use Gaufrette\Adapter\AwsS3;
use Gaufrette\Adapter\Local;
use Gaufrette\FileSystem;
use Symfony\Component\HttpFoundation\RequestStack;

class UrlGenerator
{
    /** @var FileSystem */
    protected $filesystem;

    /** @var RequestStack */
    protected $requestStack;

    /** @var string */
    protected $localDirectory;

    /**
     * @param FileSystem   $filesystem
     * @param RequestStack $requestStack
     * @param string       $localDirectory
     */
    public function __construct(FileSystem $filesystem, RequestStack $requestStack, $localDirectory)
    {
        $this->filesystem = $filesystem;
        $this->requestStack = $requestStack;
        $this->localDirectory = $localDirectory;
    }

    /**
     * Generate the full url to the original uploaded file.
     *
     * @param string $reference
     *
     * @return string
     */
    public function generate($reference)
    {
        $adapter = $this->getAdapter();
        if ($adapter instanceof AwsS3) {
            return $adapter->getUrl($reference);
        } elseif ($adapter instanceof Local) {
            $request = $this->getRequest();

            return $request->getSchemeAndHttpHost().'/'.$this->getLocalUploadDirectory().'/'.$reference;
        }

        return $reference;
    }

    /**
     * @return string
     */
    protected function getLocalUploadDirectory()
    {
        return substr($this->localDirectory, strpos($this->localDirectory, '/web/') + 5);
    }

    /**
     * @return \Gaufrette\Adapter
     */
    protected function getAdapter()
    {
        return $this->filesystem->getAdapter();
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }
}
