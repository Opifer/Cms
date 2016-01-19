<?php

namespace Opifer\MediaBundle\Tests\Provider;

use Mockery as m;
use Opifer\MediaBundle\Provider\ImageProvider;

class ImageProviderTest extends \PHPUnit_Framework_TestCase
{
    private $filesystem;
    private $translator;
    private $media;
    private $router;
    private $urlGenerator;

    private $provider;

    public function setUp()
    {
        $this->filesystem = m::mock('Gaufrette\FileSystem');
        $this->translator = m::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->media = m::mock('Opifer\MediaBundle\Tests\Media');
        $this->router = m::mock('Symfony\Component\Routing\RouterInterface');
        $this->urlGenerator = m::mock('Opifer\MediaBundle\Routing\UrlGenerator');

        $this->provider = new ImageProvider($this->filesystem, $this->translator, $this->router, $this->urlGenerator);
    }

    public function testLabelIsString()
    {
        $this->translator->shouldReceive('trans')->andReturn('image');

        $this->assertInternalType('string', $this->provider->getLabel());
    }

    public function testSingleViewIsString()
    {
        $this->assertInternalType('string', $this->provider->singleView());
    }

    public function testReturnOnPrePersistWhenFileIsEmpty()
    {
        $this->media->shouldReceive('getFile')->andReturn(null);
        $this->media->shouldReceive('getStatus')->never();

        $this->provider->prePersist($this->media);
    }

    public function testIgnoreSettingNameWhenIsNotNull()
    {
        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive(array(
            'guessExtension' => 'png',
            'getClientMimeType' => 'image/png',
            'getClientOriginalName' => 'testimage.png',
            'getClientOriginalExtension' => 'png',
            'getSize' => 2954043,
            '__toString' => __DIR__.'/../testimage.png',
        ));

        $this->filesystem->shouldReceive([
            'listKeys' => ['keys' => ['testimage-1.png']],
        ]);

        $this->media->shouldReceive(array(
            'getFile' => $file,
            'getName' => 'name',
            'getStatus' => 1,
            'setReference' => $this->media,
            'setContentType' => $this->media,
            'setFilesize' => $this->media,
            'setMetadata' => $this->media,
        ));
        $this->media->shouldReceive('setName')->never();

        $this->provider->prePersist($this->media);
    }

    public function testSetDataOnPrePersist()
    {
        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive([
            'guessExtension' => 'png',
            'getClientMimeType' => 'image/png',
            'getClientOriginalName' => 'testimage.png',
            'getClientOriginalExtension' => 'png',
            'getSize' => 2954043,
            '__toString' => __DIR__.'/../testimage.png',
        ]);

        $this->filesystem->shouldReceive([
            'listKeys' => ['keys' => ['testimage-1.png']],
        ]);

        $this->media->shouldReceive([
            'getFile' => $file,
            'getName' => null,
            'setName' => $this->media,
            'getStatus' => 1,
            'setReference' => $this->media,
            'setContentType' => $this->media,
            'setFilesize' => $this->media,
            'setMetadata' => $this->media,
        ]);

        $this->provider->prePersist($this->media);
    }

    public function tearDown()
    {
        m::close();
    }
}
