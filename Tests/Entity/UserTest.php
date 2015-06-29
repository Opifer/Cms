<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Faker\Provider\DateTime;
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

    public function testFacebookId()
    {
        $user = new User();
        $facebookId = 'Some FacebookId';

        $expected = $facebookId;
        $user->setFacebookId($facebookId);
        $actual = $user->getFacebookId();

        $this->assertSame($expected, $actual);
    }

    public function testFacebookAccessToken()
    {
        $user = new User();
        $facebookAccessToken = 'Some Facebook access token';

        $expected = $facebookAccessToken;
        $user->setFacebookAccessToken($facebookAccessToken);
        $actual = $user->getFacebookAccessToken();

        $this->assertSame($expected, $actual);
    }

    public function testGoogleId()
    {
        $user = new User();
        $googleId = 'Some Google Id';

        $expected = $googleId;
        $user->setGoogleId($googleId);
        $actual = $user->getGoogleId();

        $this->assertSame($expected, $actual);
    }

    public function testGoogleAccessToken()
    {
        $user = new User();
        $googleAccessToken = 'Some Google access token';

        $expected = $googleAccessToken;
        $user->setGoogleAccessToken($googleAccessToken);
        $actual = $user->getGoogleAccessToken();

        $this->assertSame($expected, $actual);
    }

    public function testEncoder()
    {
        $user = new User();
        $encoder = 'Some Encoder';

        $expected = $encoder;
        $user->setEncoder($encoder);
        $actual = $user->getEncoder();

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