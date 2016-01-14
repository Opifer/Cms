<?php

namespace Opifer\FormBundle\EventListener;

use Opifer\FormBundle\Event\Events;
use Opifer\FormBundle\Event\FormSubmitEvent;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Notification Email Listener.
 *
 * Listens to the `opifer.form.post_form_submit` event, which is dispatched
 * right after the the post is stored in the database.
 */
class NotificationMailListener implements EventSubscriberInterface
{
    /** @var \Swift_mailer */
    protected $mailer;

    /** @var EngineInterface */
    protected $templating;

    /** @var string */
    protected $sender;

    /**
     * Constructor.
     *
     * @param EngineInterface $templating
     * @param \Swift_mailer   $mailer
     * @param string          $sender
     */
    public function __construct(EngineInterface $templating, \Swift_mailer $mailer, $sender)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->sender = $sender;
    }

    /**
     * {@inheritDoc}
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

        if (!$form->getNotificationEmail()) {
            return;
        }

        $body = $this->templating->render('OpiferFormBundle:Email:notification.html.twig', ['post' => $post]);
        
        $message = \Swift_Message::newInstance()
            ->setSender($this->sender)
            ->setFrom($this->sender)
            ->setTo($form->getNotificationEmail())
            ->setSubject($form->getName())
            ->setBody($body)
        ;

        $this->mailer->send($message);
    }
}
