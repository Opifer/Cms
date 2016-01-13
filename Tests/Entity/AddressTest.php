<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Opifer\CmsBundle\Entity\Address;
use Doctrine\Common\Collections\ArrayCollection;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    protected $contents;
    private $id;
    private $street;

    public function testContents()
    {
        $contents = new ArrayCollection();

        $expected = $contents;
        $address = new Address();
        $actual = $address->getContents();

        $this->assertEquals($expected, $actual);
    }

    public function testStreet()
    {
        $address = new Address();
        $street = 'Some Street';

        $expected = $street;
        $address->setStreet($street);
        $actual = $address->getStreet();

        $this->assertSame($expected, $actual);
    }

    public function testZipcode()
    {
        $address = new Address();
        $zipcode = 'Some Zipcode';

        $expected = $zipcode;
        $address->setZipcode($zipcode);
        $actual = $address->getZipcode();

        $this->assertSame($expected, $actual);
    }

    public function testCity()
    {
        $address = new Address();
        $city = 'Some City';

        $expected = $city;
        $address->setCity($city);
        $actual = $address->getCity();

        $this->assertSame($expected, $actual);
    }

    public function testCountry()
    {
        $address = new Address();
        $country = 'Some Country';

        $expected = $country;
        $address->setCountry($country);
        $actual = $address->getCountry();

        $this->assertSame($expected, $actual);
    }

    public function testLat()
    {
        $address = new Address();
        $lat = 'Some Latitude';

        $expected = $lat;
        $address->setLat($lat);
        $actual = $address->getLat();

        $this->assertSame($expected, $actual);
    }

    public function testLng()
    {
        $address = new Address();
        $lng = 'Some Longitude';

        $expected = $lng;
        $address->setLng($lng);
        $actual = $address->getLng();

        $this->assertSame($expected, $actual);
    }
}
