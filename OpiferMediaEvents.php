<?php

namespace Opifer\MediaBundle;

final class OpiferMediaEvents
{
    /**
     * Index event
     *
     * Called before any actions inside the indexAction in media controllers
     * Receives the Opifer\MediaBundle\Event\ResponseEvent
     */
    const MEDIA_CONTROLLER_INDEX = 'opifer_media.controller.index';

    /**
     * New event
     *
     * Called before any actions inside the newAction in MediaController
     * Receives the Opifer\MediaBundle\Event\ResponseEvent
     */
    const MEDIA_CONTROLLER_NEW = 'opifer_media.controller.new';

    /**
     * Edit event
     * 
     * Called right after retrieving the to-be-edited media item
     * Receives the Opifer\MediaBundle\Event\MediaResponseEvent
     */
    const MEDIA_CONTROLLER_EDIT = 'opifer_media.controller.edit';

    /**
     * Update all event
     *
     * Called before any actions inside the updateAllAction in MediaController
     * Receives the Opifer\MediaBundle\Event\ResponseEvent
     */
    const MEDIA_CONTROLLER_UPDATEALL = 'opifer_media.controller.update_all';

    /**
     * Delete event
     * 
     * Called right after retrieving the to-be-deleted media item
     * Receives the Opifer\MediaBundle\Event\MediaResponseEvent
     */
    const MEDIA_CONTROLLER_DELETE = 'opifer_media.controller.delete';
}
