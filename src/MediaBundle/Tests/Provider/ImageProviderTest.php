<?php

namespace Opifer\MediaBundle\Tests\Provider;

use Gaufrette\Filesystem;
use Mockery as m;
use Opifer\MediaBundle\Provider\ImageProvider;
use Opifer\MediaBundle\Routing\UrlGenerator;
use Opifer\MediaBundle\Tests\Media;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;

class ImageProviderTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private $filesystem;
    private $translator;
    private $media;
    private $router;
    private $urlGenerator;

    private $provider;

    public function setUp(): void
    {
        $this->filesystem = m::mock(Filesystem::class);
        $this->translator = m::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->media = m::mock(Media::class);
        $this->router = m::mock(RouterInterface::class);
        $this->urlGenerator = m::mock(UrlGenerator::class);

        $this->provider = new ImageProvider($this->filesystem, $this->translator, $this->router, $this->urlGenerator);
    }

    public function testLabelIsString()
    {
        $this->translator->shouldReceive('trans')->andReturn('image');

        $this->assertIsString($this->provider->getLabel());
    }

    public function testSingleViewIsString()
    {
        $this->assertIsString($this->provider->singleView());
    }

    public function testReturnOnPrePersistWhenFileIsEmpty()
    {
        $this->media->shouldReceive('getFile')->andReturn(null);
        $this->media->shouldReceive('getStatus')->never();

        $this->provider->prePersist($this->media);
    }

    public function testIgnoreSettingNameWhenIsNotNull()
    {
        $file = m::mock(UploadedFile::class);
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

    public function tearDown(): void
    {
        m::close();
    }
}
