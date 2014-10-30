<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Model\TemplateInterface;

/**
 * FormValue
 *
 * Has a relation to a Template, which defines the formfields in the form.
 *
 * @ORM\Entity
 */
class FormValue extends Value
{
    /**
     * @var TemplateInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\TemplateInterface", cascade={"persist"})
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    protected $template;

    /**
     * Set template
     *
     * @param  TemplateInterface $template
     * @return Value
     */
    public function setTemplate(TemplateInterface $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return TemplateInterface
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return (is_null($this->template)) ? true : false;
    }
}
