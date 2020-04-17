<?php

namespace Opifer\EavBundle\Tests\Manager;

use Opifer\EavBundle\Manager\EavManager;
use Opifer\EavBundle\Tests\TestData\Schema;
use Opifer\EavBundle\Tests\TestData\TestValueProvider;
use Opifer\EavBundle\ValueProvider\Pool;
use PHPUnit\Framework\TestCase;

class EavManagerTest extends TestCase
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

        $schema = new Schema();
        $schema->setObjectClass('Opifer\EavBundle\Tests\TestData\Entity');

        $entity = $eavManager->initializeEntity($schema);

        $this->assertInstanceOf('Opifer\EavBundle\Model\EntityInterface', $entity);
    }
}
