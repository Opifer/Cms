<?php

namespace Opifer\EavBundle\Model;

interface AttributeInterface
{
    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the display name
     *
     * @return string
     */
    public function getDisplayName();

    /**
     * Get valueType
     *
     * @return string
     */
    public function getValueType();

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters();
}
