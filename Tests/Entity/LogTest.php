<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Faker\Provider\cs_CZ\DateTime;
use Opifer\CmsBundle\Entity\Log;

class LogTest extends \PHPUnit_Framework_TestCase
{
    public function testChannel()
    {
        $log = new Log();
        $channel = 'Some Channel';

        $expected = $channel;
        $log->setChannel($channel);
        $actual = $log->getChannel();

        $this->assertSame($expected, $actual);
    }

    public function testMessage()
    {
        $log = new Log();
        $message = 'Some Message';

        $expected = $message;
        $log->setMessage($message);
        $actual = $log->getMessage();

        $this->assertSame($expected, $actual);
    }

    public function testLevel()
    {
        $log = new Log();
        $level = 'Some Level';

        $expected = $level;
        $log->setLevel($level);
        $actual = $log->getLevel();

        $this->assertSame($expected, $actual);
    }

    public function testCreated()
    {
        $log = new Log();
        $created = DateTime::dateTime();

        $expected = $created;
        $log->setCreatedAt($created);
        $actual = $log->getCreated();

        $this->assertSame($expected, $actual);
    }
}
