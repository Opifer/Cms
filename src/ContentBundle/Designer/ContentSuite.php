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
     * {@inheritdoc}
     */
    public function load($id = 0)
    {
        $this->subject = $this->contentManager->getRepository()->find($id);

        if (!$this->subject) {
            throw new \Exception(sprintf('Trying to load a content item with ID %d that does not exist.', $id));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->subject->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return 'base.content';
    }

    /**
     * {@inheritdoc}
     */
    public function getPermalink()
    {
        return $this->router->generate('_content', ['slug' => $this->subject->getSlug()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertiesUrl()
    {
        return $this->router->generate('opifer_content_content_details', ['id' => $this->subject->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelUrl()
    {
        return $this->router->generate('opifer_content_content_index');
    }

    /**
     * {@inheritdoc}
     */
    public function getCanvasUrl()
    {
        return $this->router->generate('opifer_content_contenteditor_view', ['owner' => 'content', 'ownerId' => $this->subject->getId()]);
    }

    public function saveSubject()
    {
        $this->contentManager->save($this->subject);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function postPublish()
    {
        $this->subject->setUpdatedAt(new \DateTime());

        $this->contentManager->save($this->subject);

        return $this;
    }
}
