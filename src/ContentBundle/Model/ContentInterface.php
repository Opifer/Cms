<?php

namespace Opifer\ContentBundle\Model;

interface ContentInterface
{
    public function getBlocks();
    public function setBlocks($blocks);
    public function getVersion();

}
