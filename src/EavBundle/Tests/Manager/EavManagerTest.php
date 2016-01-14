<?php

namespace Opifer\EavBundle\Tests\Manager;

use Opifer\EavBundle\Manager\EavManager;
use Opifer\EavBundle\Tests\TestData\Schema;
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

        $schema = new Schema();
        $schema->setObjectClass('Opifer\EavBundle\Tests\TestData\Entity');

        $entity = $eavManager->initializeEntity($schema);

        $this->assertInstanceOf('Opifer\EavBundle\Model\EntityInterface', $entity);
    }

    public function testGetFormDataByLevel1()
    {
        $formdata = [
            'opifer_content' => [],
            'opifer_content_valueset_namedvalues_blocks' => '',
            'opifer_content_valueset_namedvalues_carousel' => '',
            'nested_content__blocks__nesting__0' => [
                'title' => 'title 1',
                '_token' => 'S0M3T0K3N'
            ],
            'nested_content__blocks__nesting__0_valueset_namedvalues_test' => '',
            'nested_content__test__nesting__0__test__nesting__0' => [
                'title' => 'title 2',
                '_token' => 'S0M3T0K3N'
            ],
            'nested_content__carousel__slide__0' => [
                'title' => 'slide 1',
                '_token' => 'S0M3T0K3N'
            ]
        ];

        $expected = [
            'nested_content__blocks__nesting__0' => [
                'title' => 'title 1',
                '_token' => 'S0M3T0K3N'
            ]
        ];

        $manager = new EavManager($this->pool, 'Opifer\EavBundle\Tests\TestData\ValueSet');

        $actual = $manager->getFormDataByLevel($formdata, 'blocks', 1, 'opifer_content');

        $this->assertEquals($expected, $actual);
    }

    public function testParseNestedTypeName()
    {
        $key = 'nested_content__blocks__nesting__0';

        $manager = new EavManager($this->pool, 'Opifer\EavBundle\Tests\TestData\ValueSet');

        $expected = [
            'level' => 1,
            'index' => 0,
            'reference' => 'nesting',
            'attribute' => 'blocks',
            'key' => 'nested_content__blocks__nesting__0'
        ];

        $actual = $manager->parseNestedTypeName($key);

        $this->assertEquals($expected, $actual);
    }

    public function testParseLevel2NestedTypeName()
    {
        $key = 'nested_content__blocks__nesting__0__blocks__nesting__3';

        $manager = new EavManager($this->pool, 'Opifer\EavBundle\Tests\TestData\ValueSet');

        $expected = [
            'level' => 2,
            'index' => 3,
            'reference' => 'nesting',
            'attribute' => 'blocks',
            'key' => 'nested_content__blocks__nesting__0__blocks__nesting__3'
        ];

        $actual = $manager->parseNestedTypeName($key);

        $this->assertEquals($expected, $actual);
    }
}
