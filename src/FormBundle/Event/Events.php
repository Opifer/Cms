<?php

namespace Opifer\FormBundle\Event;

class Events
{
    /**
     * This event is dispatched right after the post is stored in the database during the Form submit.
     */
    const POST_FORM_SUBMIT = 'opifer.form.post_form_submit';
}
