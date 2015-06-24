<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Aws\Common\Enum\Time;
use Faker\Provider\DateTime;
use Opifer\CmsBundle\Entity\Cron;
use Doctrine\ORM\Mapping as ORM;

class CronTest extends \PHPUnit_Framework_TestCase
{
    public function testCommand()
    {
        $cron = new Cron();
        $command = 'Some Command';

        $expected = $command;
        $cron->setCommand($command);
        $actual = $cron->getCommand();

        $this->assertSame($expected, $actual);
    }

    public function testExpression()
    {
        $cron = new Cron();
        $expression = 'Some Expression';

        $expected = $expression;
        $cron->setExpression($expression);
        $actual = $cron->getExpression();

        $this->assertSame($expected, $actual);
    }

    public function testPriority()
    {
        $cron = new Cron();
        $priority = 1;

        $expected = $priority;
        $cron->setPriority($priority);
        $actual = $cron->getPriority();

        $this->assertSame($expected, $actual);
    }

    public function testState()
    {
        $cron = new Cron();
        $state = 'Some State';

        $expected = $state;
        $cron->setState($state);
        $actual = $cron->getState();

        $this->assertSame($expected, $actual);
    }

    public function testRunning()
    {
        $cron = new Cron();
        $expected = false OR true;

        $actual = $cron->isRunning();

        $this->assertSame($expected, $actual);
    }

    public function testStartedAt()
    {
        $cron = new Cron();
        $dateTime = DateTime::dateTime();

        $expected = $dateTime;
        $cron->setStartedAt($dateTime);
        $actual = $cron->getStartedAt();

        $this->assertSame($expected, $actual);
    }

    public function testEndedAt()
    {
        $cron = new Cron();
        $dateTime = DateTime::dateTime();

        $expected = $dateTime;
        $cron->setEndedAt($dateTime);
        $actual = $cron->getEndedAt();

        $this->assertSame($expected, $actual);
    }

    public function testCreatedAt()
    {
        $cron = new Cron();
        $dateTime = DateTime::dateTime();

        $expected = $dateTime;
        $cron->setCreatedAt($dateTime);
        $actual = $cron->getCreatedAt();

        $this->assertSame($expected, $actual);
    }

}