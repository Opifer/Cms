<?php

namespace Opifer\CmsBundle\Tests\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializationContext;
use Opifer\CmsBundle\Entity\Content;
use Opifer\ContentBundle\Entity\ColumnBlock;
use Opifer\ContentBundle\Entity\SectionBlock;
use Opifer\ContentBundle\Serializer\BlockExclusionStrategy;
use PHPUnit\Framework\TestCase;

class ContentSerializerTest extends TestCase
{
    private function setPrivate($entity, $propertyName, $value)
    {
        $class = new \ReflectionClass($entity);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);

        $property->setValue($entity, $value);
    }

    public function testSerializeContent()
    {
        $sectionBlock = new SectionBlock();
        $this->setPrivate($sectionBlock, 'id', 1);

        $columnBlock = new ColumnBlock();
        $this->setPrivate($columnBlock, 'id', 2);
        $columnBlock->setColumnCount(2);
        $columnBlock->setParent($sectionBlock);

        $sectionBlock->setChildren(New ArrayCollection([$columnBlock]));

        $content = new Content();

        $blocks = [
            $sectionBlock
        ];

        $context = SerializationContext::create()->addExclusionStrategy(new BlockExclusionStrategy($content));
        $context->setGroups(['Default', 'tree', 'detail'])->enableMaxDepthChecks();
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        $actual = $serializer->serialize(['title' => 'Home', 'slug' => 'index', 'blocks' => $blocks], 'json', $context);

        $expected = [
            'slug' => 'index',
            'title' => 'Home',
            'blocks' => [
                [
                    'id' => 1,
                    'type' => 'SectionBlock',
                    'position' => 0,
                    'shared' => false,
                    'sort' => 0,
                    'properties' => [],
                    'active' => true,
                    'children' => [
                        [
                            'id' => 2,
                            'parentId' => 1,
                            'column_count' => 2,
                            'type' => 'ColumnBlock',
                            'position' => 0,
                            'shared' => false,
                            'sort' => 0,
                            'properties' => [],
                            'active' => true,
                            'children' => []
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, json_decode($actual, true));
    }
}
