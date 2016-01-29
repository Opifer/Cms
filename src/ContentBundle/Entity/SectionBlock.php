<?php

namespace Opifer\ContentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Block\BlockContainerInterface;

/**
 * SectionBlock
 *
 * @ORM\Entity
 */
class SectionBlock extends CompositeBlock implements BlockContainerInterface
{

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="text", name="header", nullable=true)
     */
    protected $header;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="text", name="footer", nullable=true)
     */
    protected $footer;


    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'section';
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @param string $footer
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;
    }
}