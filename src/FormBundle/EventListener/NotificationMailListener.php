<?php

namespace Opifer\FormBundle\EventListener;

use Opifer\EavBundle\Entity\EmailValue;
use Opifer\FormBundle\Event\Events;
use Opifer\FormBundle\Event\FormSubmitEvent;
use Opifer\FormBundle\Mailer\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Notification Email Listener.
 *
 * Listens to the `opifer.form.post_form_submit` event, which is dispatched
 * right after the the post is stored in the database.
 */
class NotificationMailListener implements EventSubscriberInterface
{
    /** @var Mailer */
    protected $mailer;

    /**
     * Constructor.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_FORM_SUBMIT => 'postFormSubmit',
        ];
    }

    /**
     * This method is called right after the post is stored in the database during the Form submit.
     * It checks whether the form has a notification email set, and if so, it sends out a notification
     * email.
     *
     * @param FormSubmitEvent $event
     */
    public function postFormSubmit(FormSubmitEvent $event)
    {
        $post = $event->getPost();
        $form = $post->getForm();

        if ($form->getNotificationEmail()) {
            $this->mailer->sendNotificationMail($form, $post);
        }

        if ($form->requiresConfirmation()) {
            foreach ($post->getValueSet()->getValues() as $value) {
                if ($value instanceof EmailValue && !empty($value->getValue())) {
                    $this->mailer->sendConfirmationMail($form, $post, $value->getValue());
                }
            }
        }
    }
}
