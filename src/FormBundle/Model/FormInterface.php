<?php

namespace Opifer\FormBundle\Model;

interface FormInterface
{
    /**
     * @return \Opifer\EavBundle\Model\SchemaInterface
     */
    public function getSchema();
}
