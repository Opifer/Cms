<?php

namespace Opifer\ContentBundle;

final class OpiferContentEvents
{
    /**
     * Index event
     *
     * Called before any actions inside the indexAction in content controllers
     * Receives the Opifer\ContentBundle\Event\ResponseEvent
     */
    const CONTENT_CONTROLLER_INDEX = 'opifer_content.content_controller_index';

    /**
     * view event
     *
     * Called before any actions inside the viewAction in ContentController
     * Receives the Opifer\ContentBundle\Event\ResponseEvent
     */
    const CONTENT_CONTROLLER_VIEW = 'opifer_content.content_controller_view';

    /**
     * Init event
     *
     * Called before any actions inside the newAction in ContentController
     * Receives the Opifer\ContentBundle\Event\ResponseEvent
     */
    const CONTENT_CONTROLLER_INIT = 'opifer_content.content_controller_init';

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

    /**
     * Index event
     *
     * Called before any actions inside the indexAction in content controllers
     * Receives the Opifer\ContentBundle\Event\ResponseEvent
     */
    const DIRECTORY_CONTROLLER_INDEX = 'opifer_content.directory_controller_index';

    /**
     * New event
     *
     * Called before any actions inside the newAction in ContentController
     * Receives the Opifer\ContentBundle\Event\ResponseEvent
     */
    const DIRECTORY_CONTROLLER_NEW = 'opifer_content.directory_controller_new';

    /**
     * Edit event
     * 
     * Called right after retrieving the to-be-edited content item
     * Receives the Opifer\ContentBundle\Event\ContentResponseEvent
     */
    const DIRECTORY_CONTROLLER_EDIT = 'opifer_content.directory_controller_edit';

    /**
     * Delete event
     * 
     * Called right after retrieving the to-be-deleted content item
     * Receives the Opifer\ContentBundle\Event\ContentResponseEvent
     */
    const DIRECTORY_CONTROLLER_DELETE = 'opifer_content.directory_controller_delete';
}
