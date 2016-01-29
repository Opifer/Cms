<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Opifer\CmsBundle\Entity\Address;
use Opifer\CmsBundle\Entity\AddressValue;

class AddressValueTest extends \PHPUnit_Framework_TestCase
{
    public function testAddress()
    {
        $addressValue = new AddressValue();
        $address = new Address();

        $expected = $address;
        $addressValue->setAddress($address);
        $actual = $addressValue->getAddress();

        $this->assertSame($expected, $actual);
    }
}
