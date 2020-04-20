<?php

namespace Opifer\ContentBundle\Tests\Model;

use Mockery as m;
use Opifer\ContentBundle\Model\ContentManager;
use PHPUnit\Framework\TestCase;

class ContentManagerTest extends TestCase
{
    private $em;
    private $formFactory;
    private $eavManager;

    private $contentClass = 'Opifer\ContentBundle\Tests\TestData\Content';
    private $templateClass = 'Opifer\ContentBundle\Tests\TestData\Template';

    public function setUp(): void
    {
        $this->em = m::mock('Doctrine\ORM\EntityManager');
        $this->formFactory = m::mock('Symfony\Component\Form\FormFactory');
        $this->eavManager = m::mock('Opifer\EavBundle\Manager\EavManager');
    }

    public function testGetClass()
    {
        $manager = new ContentManager($this->em, $this->formFactory, $this->eavManager, $this->contentClass, $this->templateClass);

        $this->assertEquals('Opifer\ContentBundle\Tests\TestData\Content', $manager->getClass());
    }

    public function tearDown(): void
    {
        m::close();
    }
}
