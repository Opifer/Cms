<?php

namespace Opifer\MediaBundle\Model;

interface MediaInterface
{
    public function getName();

    public function getReference();

    public function getProvider();

    public function getFile();

    public function setOriginal($original);
}
