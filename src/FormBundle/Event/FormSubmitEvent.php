<?php

namespace Opifer\FormBundle\Event;

use Opifer\FormBundle\Model\PostInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Form Submit Event.
 *
 * Holds the submitted and stored Post entity
 */
class FormSubmitEvent extends Event
{
    /** @var PostInterface */
    protected $post;

    /**
     * Constructor.
     *
     * @param PostInterface $post
     */
    public function __construct(PostInterface $post)
    {
        $this->post = $post;
    }

    /**
     * @return PostInterface
     */
    public function getPost()
    {
        return $this->post;
    }
}
