<?php
/**
 * Created by PhpStorm.
 * User: dylan
 * Date: 21/01/16
 * Time: 09:48
 */

namespace Opifer\ContentBundle\Designer;

use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;

abstract class AbstractDesignContext implements DesignContextInterface
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
     * @return object
     */
    public function load($id = 0, $version = 1)
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
    public function getBlock()
    {
        return $this->subject->getBlock();
    }

    /**
     * @param BlockInterface $block
     *
     * @return DesignContextInterface
     */
    public function setBlock(BlockInterface $block)
    {
        $this->subject->setBlock($block);
        $this->block = $block;

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
}