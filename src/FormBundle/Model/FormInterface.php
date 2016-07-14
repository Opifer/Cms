<?php

namespace Opifer\FormBundle\Model;

interface FormInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return \Opifer\EavBundle\Model\SchemaInterface
     */
    public function getSchema();
}
