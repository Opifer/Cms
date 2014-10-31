<?php

namespace Opifer\EavBundle\Tests\Manager;

use Opifer\EavBundle\Manager\EavManager;
use Opifer\EavBundle\Tests\TestData\Template;
use Opifer\EavBundle\Tests\TestData\TestValueProvider;
use Opifer\EavBundle\ValueProvider\Pool;

class EavManagerTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        $this->pool = new Pool();

        $provider = new TestValueProvider();
        $this->pool->addValue($provider, 'test');
    }

    public function testInitializeEntity()
    {
        $eavManager = new EavManager($this->pool, 'Opifer\EavBundle\Tests\TestData\ValueSet');

        $template = new Template();
        $template->setObjectClass('Opifer\EavBundle\Tests\TestData\Entity');

        $entity = $eavManager->initializeEntity($template);

        $this->assertInstanceOf('Opifer\EavBundle\Model\EntityInterface', $entity);
    }
}
