<?php

namespace Opifer\FormBundle\Model;

interface FormInterface
{
    /**
     * @return \Opifer\EavBundle\Model\TemplateInterface
     */
    public function getTemplate();
}
