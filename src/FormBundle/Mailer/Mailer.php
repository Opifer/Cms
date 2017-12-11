<?php

namespace Opifer\FormBundle\Mailer;

use Opifer\EavBundle\Entity\EmailValue;
use Opifer\FormBundle\Event\Events;
use Opifer\FormBundle\Event\FormSubmitEvent;
use Opifer\FormBundle\Model\FormInterface;
use Opifer\FormBundle\Model\PostInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Mailer
 */
class Mailer
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
    public function __construct(RequestStack $request, EngineInterface $templating, \Swift_mailer $mailer, $sender)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->sender = $sender;
        $this->request = $request;
    }

    /**
     * @param FormInterface $form
     * @param PostInterface $post
     */
    public function sendNotificationMail(FormInterface $form, PostInterface $post)
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
    public function sendConfirmationMail(FormInterface $form, PostInterface $post, $recipient)
    {
        $this->request->getCurrentRequest()->setLocale($form->getLocale());
        $body = $this->templating->render('OpiferFormBundle:Email:confirmation.html.twig', ['post' => $post]);

        $message = $this->createMessage($recipient, $form->getName(), $body);

        $this->send($message);
    }

    /**
     * @param string $recipient
     * @param string $subject
     * @param string $body
     *
     * @return \Swift_Mime_Message
     */
    public function createMessage($recipient, $subject, $body)
    {
        $recipients = explode(',', trim($recipient));

        return \Swift_Message::newInstance()
            ->setSender($this->sender)
            ->setFrom($this->sender)
            ->setTo($recipients)
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
