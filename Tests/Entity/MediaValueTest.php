<?php

namespace Opifer\EavBundle\Tests\Entity;

use Opifer\EavBundle\Entity\MediaValue;
use Opifer\EavBundle\Tests\TestData\Media;

class MediaValueTest extends \PHPUnit_Framework_TestCase
{
    protected $value;

    public function setUp()
    {
        $this->value = new MediaValue();
    }

    public function testCountable()
    {
        $media = new Media();
        $media->setName('image1')->setReference('image1.jpg');

        $media2 = new Media();
        $media2->setName('image2')->setReference('image2.jpg');

        $this->value->addMedia($media);
        $this->value->addMedia($media2);

        $this->assertCount(2, count($this->value));
    }
}
