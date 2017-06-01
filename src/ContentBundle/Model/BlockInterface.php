<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

interface BlockInterface
{
    public function getId();

    /**
     * The string returned should match Block's service name.
     *
     * @return string
     */
    public function getBlockType();

    public function isInRoot();

    /**
     * @return ContentInterface
     */
    public function getOwner();

    /**
     * @param ContentInterface $owner
     *
     * @return BlockInterface
     */
    public function setOwner(ContentInterface $owner);

    /**
     * @param BlockInterface $parent
     */
    public function setParent($parent);

    public function setDraft($draft);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * Should return an array of block properties.
     *
     * @return array
     */
    public function getProperties();

    /**
     * Returns the content.
     *
     * @return ContentInterface
     */
    public function getContent();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @return ArrayCollection|BlockInterface[]
     */
    public function getChildren();
}
