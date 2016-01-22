<?php
/**
 * Created by PhpStorm.
 * User: dylan
 * Date: 22/01/16
 * Time: 11:03
 */

namespace Opifer\ContentBundle\Environment;

use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\ContentManager;
use Opifer\ContentBundle\Model\BlockInterface;

class ContentEnvironment extends TemplateEnvironment
{
    /**
     * @var ContentInterface
     */
    protected $content;
    /**
     * @var ContentManager
     */
    protected $contentManager;

    /**
     * {@inheritDoc}
     */
    public function load($id = 0, $version = false)
    {
        $this->content = $this->contentManager->getRepository()->find($id);

        if ($version) {
            $this->versionMap[$this->content->getBlock()->getId()] = $version;
        }

        if ( ! $this->content) {
            throw $this->createNotFoundException('No content found for id ' . $id);
        }

        $this->setTemplate($this->content->getTemplate());

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function getBlockOwners()
    {
        $blockOwners = parent::getBlockOwners();

        $blockOwners[] = $this->content->getBlock();

        return $blockOwners;
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        $parameters = array(
            'content'  => $this->content,
        );

        return array_merge(parent::getViewParameters(), $parameters);
    }

    /**
     * @param BlockInterface $block
     *
     * @return string
     */
    public function getBlockMode(BlockInterface $block = null)
    {
        if ($block && $block->getOwner()->getId() != $this->content->getBlock()->getId()) {
            return 'default';
        }

        return $this->blockMode;
    }

    /**
     * @return ContentInterface
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param ContentInterface $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return ContentManager
     */
    public function getContentManager()
    {
        return $this->contentManager;
    }

    /**
     * @param ContentManager $contentManager
     */
    public function setContentManager($contentManager)
    {
        $this->contentManager = $contentManager;
    }
}