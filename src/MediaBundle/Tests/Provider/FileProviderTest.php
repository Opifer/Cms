<?php

namespace Opifer\MediaBundle\Tests\Provider;

use Mockery as m;
use Opifer\MediaBundle\Form\Type\DropzoneType;
use Opifer\MediaBundle\Provider\FileProvider;

class FileProviderTest extends \PHPUnit_Framework_TestCase
{
    private $filesystem;
    private $translator;
    private $media;
    private $router;
    private $urlGenerator;

    public function setUp()
    {
        $this->filesystem = m::mock('Gaufrette\FileSystem');
        $this->translator = m::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->media = m::mock('Opifer\MediaBundle\Tests\Media');
        $this->router = m::mock('Symfony\Component\Routing\RouterInterface');
        $this->urlGenerator = m::mock('Opifer\MediaBundle\Routing\UrlGenerator');

        $this->provider = new FileProvider($this->filesystem, $this->translator, $this->router, $this->urlGenerator);
    }

    public function testFormHasDropzoneField()
    {
        $this->router->shouldReceive('generate')->andReturn('/generated/path');

        $builder = m::mock('Symfony\Component\Form\FormBuilderInterface');
        $builder->shouldReceive('add')->with('files', DropzoneType::class, [
            'mapped' => false,
            'path' => '/generated/path',
            'form_action' => '/generated/path',
            'label' => '',
        ]);

        $this->provider->buildCreateForm($builder, array());
    }

    public function testPrePersistWithEmptyFile()
    {
        $this->media->shouldReceive('getFile')->andReturn(null);
        $this->media->shouldReceive('setReference')->never();

        $this->provider->prePersist($this->media);
    }

    public function testPrePersistSetsReference()
    {
        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive(array(
            'guessExtension' => 'jpg',
            'getClientMimeType' => 'image/jpeg',
            'getSize' => 2954043,
            'getClientOriginalExtension' => 'jpg',
            'getClientOriginalName' => 'testimage.png',
        ));

        $this->media->shouldReceive(array(
            'getFile' => $file,
            'setReference' => $this->media,
            'setContentType' => $this->media,
            'setFilesize' => $this->media,
            'setMetadata' => $this->media,
            'getStatus' => 1,
            'getName' => 'Testname',
        ));

        $this->filesystem->shouldReceive([
            'listKeys' => ['keys' => [
                'testimage.png',
                'testimage-1.png',
                'testiamge-3.png',
            ]],
        ]);

        $this->provider->prePersist($this->media);
    }

    public function testPostRemoveDeletesFile()
    {
        $this->media->shouldReceive('getReference')->andReturn('filename.jpg');
        $this->filesystem->shouldReceive('has')->with('filename.jpg');
        $this->filesystem->shouldReceive('delete')->with('filename.jpg');

        $this->provider->postRemove($this->media);
    }

    public function testIgnoreDeleteOnEmptyReference()
    {
        $this->media->shouldReceive('getReference')->andReturn(null);
        $this->filesystem->shouldReceive('has')->with(null);
        $this->filesystem->shouldReceive('delete')->never();

        $this->provider->postRemove($this->media);
    }

    public function testUploadFile()
    {
        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('__toString')->andReturn(__DIR__.'/../testfile.txt');

        $this->media->shouldReceive(array(
            'getFile' => $file,
            'getReference' => 'filename.jpg',
            'setFile' => $this->media,
        ));
        $this->filesystem->shouldReceive('getAdapter');
        $this->filesystem->shouldReceive('write')->with('filename.jpg', 'content');

        $this->provider->upload($this->media);
    }

    public function testIgnoreUploadOnEmptyFile()
    {
        $this->media->shouldReceive('getFile')->andReturn(null);
        $this->filesystem->shouldReceive('write')->never();

        $this->provider->upload($this->media);
    }

    public function testCreateUniqueFileName()
    {
        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive([
            'guessExtension' => 'png',
            'getClientOriginalName' => 'testimage.png',
            'getClientOriginalExtension' => 'png',
        ]);

        $this->filesystem->shouldReceive([
            'listKeys' => ['keys' => [
                'testimage.png',
                'testimage-1.png',
                'testiamge-3.png',
            ]],
        ]);

        $filename = $this->provider->createUniqueFileName($file);

        $this->assertEquals('testimage-4.png', $filename);
    }

    public function tearDown()
    {
        m::close();
    }
}
