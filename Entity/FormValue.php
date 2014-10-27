<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Opifer\EavBundle\Eav\ValueInterface;

/**
 * FormValue
 *
 * Has a relation to a Template, which defines the formfields in the form.
 *
 * @ORM\Entity
 */
class FormValue extends Value implements ValueInterface
{
    /**
     * @var  \Opifer\EavBundle\Entity\Template
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Entity\Template", cascade={"persist"})
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    protected $template;

    /**
     * Set template
     *
     * @param  \Opifer\EavBundle\Entity\Template $template
     * @return Value
     */
    public function setTemplate(Template $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \Opifer\EavBundle\Entity\Template
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
