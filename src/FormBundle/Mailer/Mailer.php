<?php

namespace Opifer\FormBundle\Mailer;

use Opifer\FormBundle\Model\FormInterface;
use Opifer\FormBundle\Model\PostInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

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

    /** @var TranslatorInterface */
    protected $translator;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * Mailer constructor.
     *
     * @param TranslatorInterface $translator
     * @param RequestStack        $requestStack
     * @param EngineInterface     $templating
     * @param \Swift_mailer       $mailer
     * @param                     $sender
     */
    public function __construct(TranslatorInterface $translator, RequestStack $requestStack, EngineInterface $templating, \Swift_mailer $mailer, $sender)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->sender = $sender;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * @param FormInterface $form
     * @param PostInterface $post
     */
    public function sendNotificationMail(FormInterface $form, PostInterface $post)
    {
        $body = $this->templating->render('OpiferFormBundle:Email:notification.html.twig', ['post' => $post]);
        $subject = $form->getName() . ' (post id:' . $post->getId() . ')';

        $message = $this->createMessage($form->getNotificationEmail(), $subject, $body);

        $this->send($message);
    }

    /**
     * @param FormInterface $form
     * @param PostInterface $post
     * @param string        $recipient
     */
    public function sendConfirmationMail(FormInterface $form, PostInterface $post, $recipient)
    {
        if ($form->getLocale()) {
            $this->requestStack->getCurrentRequest()->setLocale($form->getLocale()->getLocale());
            $this->translator->setLocale($form->getLocale()->getLocale());
        }

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
