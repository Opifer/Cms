<?php

namespace Opifer\EavBundle\Tests\Manager;

use Opifer\EavBundle\Manager\EavManager;
use Opifer\EavBundle\Model\EntityInterface;
use Opifer\EavBundle\Tests\TestData\Entity;
use Opifer\EavBundle\Tests\TestData\Schema;
use Opifer\EavBundle\Tests\TestData\TestValueProvider;
use Opifer\EavBundle\Tests\TestData\ValueSet;
use Opifer\EavBundle\ValueProvider\Pool;
use PHPUnit\Framework\TestCase;

class EavManagerTest extends TestCase
{
    public function __construct()
    {
        parent::__construct();

        $this->pool = new Pool();

        $provider = new TestValueProvider();
        $this->pool->addValue($provider, 'test');
    }

    public function testInitializeEntity()
    {
        $eavManager = new EavManager($this->pool, ValueSet::class);

        $schema = new Schema();
        $schema->setObjectClass(Entity::class);

        $entity = $eavManager->initializeEntity($schema);

        $this->assertInstanceOf(EntityInterface::class, $entity);
    }
}
