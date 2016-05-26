<?php

namespace Opifer\ContentBundle\Model;

interface ContentTypeInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();
}
