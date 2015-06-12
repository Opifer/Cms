<?php

namespace Opifer\CmsBundle\EventListener;

use Doctrine\ORM\Event;
use Opifer\CmsBundle\Entity\Post;
use Symfony\Component\DependencyInjection\Container;
use Opifer\EavBundle\Entity\OptionValue;
use Opifer\EavBundle\Entity\DateTimeValue;

class PostNotifyListener
{
    protected $container;
    protected $mailer;
    protected $twig;
    protected $servicesLoaded = false;
    protected $inserted_entities = [];
    protected $admin_email;

    /**
     * Constructor
     *
     * @param Container $sc
     */
    public function __construct(Container $sc)
    {
        $this->container = $sc;
    }

    /**
     *
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $event
     */
    public function onFlush(Event\OnFlushEventArgs $event)
    {
        $em  = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->inserted_entities[] = $entity;
        }
    }

    /**
     *
     * @param \Doctrine\ORM\Event\PostFlushEventArgs $args
     */
    public function postFlush(Event\PostFlushEventArgs $args)
    {
        foreach ($this->inserted_entities as $entity) {
            if ($entity instanceof Post && $entity->getId() > 0) {
                $email = $entity->getTemplate()->getPostNotify();

                if ($email) {
                    if (!$this->servicesLoaded) {
                        $this->loadServices();
                    }

                    $post = [];

                    foreach ($entity->getValueSet()->getValues() as $value) {
                        $post[$value->getAttribute()->getDisplayName()] = [];

                        if ($value instanceof OptionValue) {
                            $post_value = [];

                            foreach ($value->getOptions() as $option) {
                                $post_value[] = $option->getDisplayName();
                            }
                        } elseif ($value instanceof DateTimeValue) {
                            $post_value = [date("m/d/Y", $value->getValue()->getTimestamp())];
                        } else {
                            $post_value = [$value->getValue()];
                        }

                        $post[$value->getAttribute()->getDisplayName()] = $post_value;
                    }

                    $txt_template = "OpiferCmsBundle:Email:post-notify.txt.twig";
                    $txt_body = $this->twig->render($txt_template, ['post' => $post]);

                    $message = \Swift_Message::newInstance()
                        ->setSender($this->admin_email)
                        ->setFrom($this->admin_email)
                        ->setTo($email)
                        ->setBody($txt_body)
                    ;

                    $this->sendEmail($message);
                }
            }
        }
    }

    /**
     * Send email
     *
     * @param array $message
     */
    private function sendEmail($message)
    {
        $this->mailer->send($message);
    }

    /**
     * Get services
     */
    protected function loadServices()
    {
        $this->mailer = $this->container->get("mailer");
        $this->twig   = $this->container->get("templating");
        $this->admin_email = $this->container->getParameter('admin_email');
        $this->servicesLoaded = true;
    }
}
