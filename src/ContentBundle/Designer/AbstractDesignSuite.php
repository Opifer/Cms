<?php

namespace Opifer\ContentBundle\Designer;

use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractDesignSuite implements DesignSuiteInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var object
     */
    protected $subject;

    /**
     * @var int
     */
    protected $version;

    /**
     * @return AbstractDesignSuite
     */
    public function load($id = 0)
    {
        throw new NotImplementedException('Design Context must implement load method');
    }


    /**
     * Displays in navbar at the top of the designer window.
     *
     * @return string
     */
    public function getTitle()
    {
        return 'content.editor.designer';
    }

    /**
     * Displays in navbar at the top of the designer window.
     *
     * @return string
     */
    public function getCaption()
    {
        return '';
    }

    /**
     * Used as a preview link to open in a new browser window.
     *
     * @return bool|string
     */
    public function getPermalink()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getPropertiesUrl()
    {
        throw new NotImplementedException('Design Context must implement getPropertiesUrl method');
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        throw new NotImplementedException('Design Context must implement getCancelUrl method');
    }

    /**
     * @return string
     */
    public function getCanvasUrl()
    {
        throw new NotImplementedException('Design Context must implement getCanvasUrl method');
    }

    /**
     * @return mixed
     */
    public function getBlocks()
    {
        return $this->subject->getBlocks();
    }

    /**
     * @param array $block
     *
     * @return DesignContextInterface
     */
    public function setBlocks($blocks)
    {
        $this->subject->setBlock($blocks);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     *
     * @return DesignContextInterface
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return Environment
     */
    public function getEnvironment()
    {
        throw new NotImplementedException('Design Context must implement getEnvironment method');
    }

    /**
     * Event hook for post publishing actions
     */
    public function postPublish()
    {

    }
}