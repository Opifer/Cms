<?php

namespace Opifer\ContentBundle\Model;

interface LayoutInterface
{
    /**
     * @return string
     */
    public function getFilename();

    /**
     * @return string
     */
    public function getAction();
}
