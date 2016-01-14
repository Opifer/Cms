<?php

namespace Opifer\ContentBundle\Tests\Model;

use Opifer\ContentBundle\Model\Content;
use Opifer\ContentBundle\Model\Directory;
use Opifer\ContentBundle\Tests\TestData\Schema;
use Opifer\EavBundle\Entity\NestedValue;
use Opifer\EavBundle\Entity\Value;
use Opifer\EavBundle\Tests\TestData\ValueSet;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $content = $this->getContent();
        $this->assertNull($content->getId());
    }

    public function testValueSet()
    {
        $content = $this->getContent();
        $this->setExpectedException('Exception', 'Make sure to give Content a ValueSet on creation');
        $content->getValueSet();

        $valueSet = new ValueSet();

        $content->setValueSet($valueSet);
        $this->assertEquals($valueSet, $content->getValueSet());
    }

    public function testActive()
    {
        $content = $this->getContent();
        $this->assertTrue($content->getActive());

        $content->setActive(false);
        $this->assertFalse($content->getActive());
    }

    public function testTitle()
    {
        $content = $this->getContent();
        $this->assertNull($content->getTitle());

        $content->setTitle('title');
        $this->assertEquals('title', $content->getTitle());
    }

    public function testDescription()
    {
        $content = $this->getContent();
        $this->assertNull($content->getDescription());

        $content->setDescription('description');
        $this->assertEquals('description', $content->getDescription());
    }

    public function testPresentation()
    {
        $content = $this->getContent();
        $this->assertNull($content->getPresentation());

        $content->setPresentation('presentation');
        $this->assertEquals('presentation', $content->getPresentation());
    }

    public function testAlias()
    {
        $content = $this->getContent();
        $this->assertNull($content->getAlias());

        $content->setAlias('alias');
        $this->assertEquals('alias', $content->getAlias());
    }

    public function testSymlink()
    {
        $content = $this->getContent();
        $this->assertNull($content->getSymlink());

        $content = new Content();

        $content->setSymlink($content);
        $this->assertEquals($content, $content->getSymlink());
    }

    public function testSlug()
    {
        $content = $this->getContent();
        $this->assertNull($content->getSlug());

        $content->setSlug('slug');
        $this->assertEquals('slug', $content->getSlug());

        $this->assertEquals('slug', $content->getBaseSlug());
        $content->setSlug('slug/index');
        $this->assertEquals('slug/', $content->getBaseSlug());
    }

    public function testDirectory()
    {
        $content = $this->getContent();
        $this->assertNull($content->getDirectory());

        $directory = new Directory();

        $content->setDirectory($directory);
        $this->assertEquals($directory, $content->getDirectory());
    }

    public function testCreatedAt()
    {
        $content = $this->getContent();
        $this->assertNull($content->getCreatedAt());

        $dateTime = new \DateTime();

        $content->setCreatedAt($dateTime);
        $this->assertEquals($dateTime, $content->getCreatedAt());
    }

    public function testUpdatedAt()
    {
        $content = $this->getContent();
        $this->assertNull($content->getUpdatedAt());

        $dateTime = new \DateTime();

        $content->setUpdatedAt($dateTime);
        $this->assertEquals($dateTime, $content->getUpdatedAt());
    }

    public function testDeletedAt()
    {
        $content = $this->getContent();
        $this->assertNull($content->getDeletedAt());

        $dateTime = new \DateTime();

        $content->setDeletedAt($dateTime);
        $this->assertEquals($dateTime, $content->getDeletedAt());
    }

    public function testAttributeValue()
    {
        $content = $this->getContent();
        $this->assertTrue($content->getAttributeValues()->isEmpty());

        $value = new Value();

        $content = $this->getContent();
        $content->addAttributeValue($value);
        $this->assertFalse($content->getAttributeValues()->isEmpty());
        $this->assertTrue($content->getAttributeValues()->contains($value));

        $content->removeAttributeValue($value);
        $this->assertTrue($content->getAttributeValues()->isEmpty());
        $this->assertFalse($content->getAttributeValues()->contains($value));
    }

    public function testSchema()
    {
        $content = $this->getContent();

        $schema = new Schema();

        $content->setValueSet(new ValueSet());
        $content->setSchema($schema);
        $this->assertEquals($schema, $content->getSchema());

        $content = $this->getContent();
        $this->setExpectedException('Exception', 'Make sure to give Content a ValueSet on creation');
        $content->getSchema();

    }

    private function getContent()
    {
        return new Content();
    }

    private function getContentWithSchema()
    {
        $schema = new Schema();

        $valueSet = new ValueSet();
        $valueSet->setSchema($schema);

        $content = new Content();
        $content->setValueSet($valueSet);

        return $content;
    }
}