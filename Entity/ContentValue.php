<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Entity\Value;
use Opifer\EavBundle\Model\ValueInterface;
//use Opifer\ContentBundle\Model\ContentInterface;

/**
 * ContentValue
 *
 * @ORM\Entity
 */
class ContentValue extends Value
{
    
    /**
     * @var <Content>
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\ContentInterface")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     *
     */
    protected $content;
    
    /**
     * Turn value into string for
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
    
    /**
     * Get the value
     *
     * Overrides the parent getValue method
     *
     * @return Content
     */
    public function getValue()
    {
        return $this->content;
    }
    
    /**
     * Set content
     *
     * @param Content $content
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return Content
     */
    public function getContent()
    {
//        dump($this->content);
        return $this->content;
    }
}
