<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Opifer\CmsBundle\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testFirstName()
    {
        $user = new User();
        $firstName = 'Some Firstname';

        $expected = $firstName;
        $user->setFirstName($firstName);
        $actual = $user->getFirstName();

        $this->assertSame($expected, $actual);
    }

    public function testLastName()
    {
        $user = new User();
        $lastName = 'Some Lastname';

        $expected = $lastName;
        $user->setLastName($lastName);
        $actual = $user->getLastName();

        $this->assertSame($expected, $actual);
    }

    public function testDeletedAt()
    {
        $user = new User();
        $deletedAt = new \DateTime();

        $expected = $deletedAt;
        $user->setDeletedAt($deletedAt);
        $actual = $user->getDeletedAt();

        $this->assertSame($expected, $actual);
    }
}
