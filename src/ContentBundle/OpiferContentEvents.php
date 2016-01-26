<?php

namespace Opifer\ContentBundle;

final class OpiferContentEvents
{
    /**
     * view event
     *
     * Called before any actions inside the viewAction in ContentController
     * Receives the Opifer\ContentBundle\Event\ResponseEvent
     */
    const CONTENT_CONTROLLER_VIEW = 'opifer_content.content_controller_view';

    /**
     * New event
     *
     * Called before any actions inside the newAction in ContentController
     * Receives the Opifer\ContentBundle\Event\ResponseEvent
     */
    const CONTENT_CONTROLLER_NEW = 'opifer_content.content_controller_new';

    /**
     * Edit event
     * 
     * Called right after retrieving the to-be-edited content item
     * Receives the Opifer\ContentBundle\Event\ContentResponseEvent
     */
    const CONTENT_CONTROLLER_EDIT = 'opifer_content.content_controller_edit';

    /**
     * Delete event
     * 
     * Called right after retrieving the to-be-deleted content item
     * Receives the Opifer\ContentBundle\Event\ContentResponseEvent
     */
    const CONTENT_CONTROLLER_DELETE = 'opifer_content.content_controller_delete';
}
