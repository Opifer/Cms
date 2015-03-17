<?php

namespace Opifer\EavBundle\Tests\TestData;

use Opifer\EavBundle\Model\MediaInterface;

class Media implements MediaInterface
{
    protected $name;
    protected $reference;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    public function getReference()
    {
        return $this->reference;
    }
}
