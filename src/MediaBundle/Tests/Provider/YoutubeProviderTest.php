<?php

namespace Opifer\MediaBundle\Tests\Provider;

use Mockery as m;
use Opifer\MediaBundle\Provider\YoutubeProvider;
use PHPUnit\Framework\TestCase;

class YoutubeProviderTest extends TestCase
{
    private $mediaManager;
    private $translator;
    private $media;

    public function setUp(): void
    {
        $this->mediaManager = m::mock('Opifer\MediaBundle\Model\MediaManager');
        $this->translator = m::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->media = m::mock('Opifer\MediaBundle\Tests\Media');
    }

    public function testLabelIsString()
    {
        $this->translator->shouldReceive('trans')->andReturn('image');

        $youtubeProvider = new YoutubeProvider($this->mediaManager, $this->translator, 'apikeystring');
        $this->assertIsString($youtubeProvider->getLabel());
    }

    public function testChangeDurationToDigits()
    {
        $youtubeProvider = new YoutubeProvider($this->mediaManager, $this->translator, 'apikeystring');

        $this->assertEquals('00:02:03', $youtubeProvider->convertDuration('PT2M3S'));
        $this->assertEquals('01:17:22', $youtubeProvider->convertDuration('PT1H17M22S'));
    }

    public function testUpdateMetadata()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testGetMetadata()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testPreSave()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSaveThumbnail()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function tearDown(): void
    {
        m::close();
    }
}
