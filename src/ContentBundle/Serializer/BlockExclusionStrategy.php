<?php

namespace Opifer\ContentBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Model\Content;

class BlockExclusionStrategy implements ExclusionStrategyInterface
{
    /** @var Content */
    protected $content;

    /**
     * Constructor
     *
     * @param Content $content
     */
    public function __construct(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Skip blocks of a content item that are children of a template block and do not match the content id.
     * The reason for this is that template blocks could have children from different content items
     *
     * {@inheritdoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $context)
    {
        $obj = null;
        // Get the last item of the visiting set
        foreach ($context->getVisitingSet() as $item) {
            $obj = $item;
        }

        if ($obj && $obj instanceof Block && $obj->getParent() && $obj->getParent()->getTemplate() && $obj->getContent() && $obj->getContentId() != $this->content->getId()) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $context)
    {
        return false;
    }
}
