<?php

namespace Opifer\FormBundle\EventListener;

use Opifer\EavBundle\Entity\EmailValue;
use Opifer\FormBundle\Event\Events;
use Opifer\FormBundle\Event\FormSubmitEvent;
use Opifer\FormBundle\Model\FormInterface;
use Opifer\FormBundle\Model\PostInterface;
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
            $this->sendNotificationMail($form, $post);
        }

        if ($form->requiresConfirmation()) {
            foreach ($post->getValueSet()->getValues() as $value) {
                if ($value instanceof EmailValue && !empty($value->getValue())) {
                    $this->sendConfirmationMail($form, $post, $value->getValue());
                }
            }
        }
    }

    /**
     * @param FormInterface $form
     * @param PostInterface $post
     */
    protected function sendNotificationMail(FormInterface $form, PostInterface $post)
    {
        $body = $this->templating->render('OpiferFormBundle:Email:notification.html.twig', ['post' => $post]);

        $message = $this->createMessage($form->getNotificationEmail(), $form->getName(), $body);

        $this->send($message);
    }

    /**
     * @param FormInterface $form
     * @param PostInterface $post
     * @param string        $recipient
     */
    protected function sendConfirmationMail(FormInterface $form, PostInterface $post, $recipient)
    {
        $body = $this->templating->render('OpiferFormBundle:Email:confirmation.html.twig', ['post' => $post]);

        $message = $this->createMessage($recipient, $form->getName(), $body);

        $this->send($message);
    }

    /**
     * @param $recipient
     * @param $subject
     * @param $body
     *
     * @return \Swift_Mime_Message
     */
    protected function createMessage($recipient, $subject, $body)
    {
        return \Swift_Message::newInstance()
            ->setSender($this->sender)
            ->setFrom($this->sender)
            ->setTo($recipient)
            ->setSubject($subject)
            ->setBody($body, 'text/html');
    }

    /**
     * @param \Swift_Mime_Message $message
     *
     * @return int
     */
    protected function send(\Swift_Mime_Message $message)
    {
        return $this->mailer->send($message);
    }
}
