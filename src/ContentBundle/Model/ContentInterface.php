<?php

namespace Opifer\ContentBundle\Model;

use Opifer\ContentBundle\Entity\Template;

interface ContentInterface
{
    public function getBlocks();
    public function setBlocks($blocks);
    public function getVersion();

    /**
     * @return Template
     */
    public function getTemplate();
}
