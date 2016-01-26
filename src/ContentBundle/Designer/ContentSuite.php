<?php

namespace Opifer\ContentBundle\Designer;

use Opifer\ContentBundle\Model\ContentManager;
use Symfony\Component\Routing\RouterInterface;

class ContentSuite extends AbstractDesignSuite
{
    /**
     * @var ContentManager
     */
    protected $contentManager;


    public function __construct(RouterInterface $router, ContentManager $contentManager)
    {
        $this->router = $router;
        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritDoc}
     */
    public function load($id = 0, $version = 1)
    {
        $this->subject = $this->contentManager->getRepository()->find($id);
        $this->version = $version;

        if (!$this->subject) {
            throw $this->createNotFoundException('No content found for id ' . $id);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->subject->getTitle();
    }

    /**
     * {@inheritDoc}
     */
    public function getCaption()
    {
        return 'base.content';
    }

    /**
     * {@inheritDoc}
     */
    public function getPermalink()
    {
        return $this->router->generate('_content', ['slug' => $this->subject->getSlug()]);
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertiesUrl()
    {
        return $this->router->generate('opifer_content_content_details', ['id' => $this->subject->getId()]);
    }

    /**
     * {@inheritDoc}
     */
    public function getCancelUrl()
    {
        return $this->router->generate('opifer_content_content_index');
    }

    /**
     * {@inheritDoc}
     */
    public function getCanvasUrl($version)
    {
        return $this->router->generate('opifer_content_contenteditor_view', ['type' => 'content', 'id' => $this->subject->getId(), 'version' => $version]);
    }


    public function saveSubject()
    {
        $this->contentManager->save($this->subject);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function postPublish()
    {
        $this->subject->setUpdatedAt(new \DateTime);

        $this->contentManager->save($this->subject);

        return $this;
    }
}