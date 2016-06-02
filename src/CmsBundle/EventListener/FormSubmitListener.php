<?php

namespace Opifer\CmsBundle\EventListener;

use Opifer\CmsBundle\Entity\MailingListSubscribeValue;
use Opifer\EavBundle\Entity\EmailValue;
use Opifer\FormBundle\Event\Events;
use Opifer\FormBundle\Event\FormSubmitEvent;
use Opifer\MailingListBundle\Entity\Subscription;
use Opifer\MailingListBundle\Manager\MailingListManager;
use Opifer\MailingListBundle\Manager\SubscriptionManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Form Submit Listener.
 *
 * Listens to the `opifer.form.post_form_submit` event, which is dispatched
 * right after the the post is stored in the database.
 */
class FormSubmitListener implements EventSubscriberInterface
{
    /**
     * @var MailingListManager
     */
    protected $mailingListManager;

    /**
     * @var SubscriptionManager
     */
    protected $subscriptionManager;

    /**
     * Constructor.
     *
     * @param MailingListManager $mailingListManager
     * @param SubscriptionManager $subscriptionManager
     */
    public function __construct(MailingListManager $mailingListManager, SubscriptionManager $subscriptionManager)
    {
        $this->mailingListManager = $mailingListManager;
        $this->subscriptionManager = $subscriptionManager;
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
     *
     * @param FormSubmitEvent $event
     */
    public function postFormSubmit(FormSubmitEvent $event)
    {
        $post = $event->getPost();

        $mailinglists = $email = null;
        foreach ($post->getValueSet()->getValues() as $value) {
            if ($value instanceof MailingListSubscribeValue && $value->getValue() == true) {
                $parameters = $value->getAttribute()->getParameters();
                if (isset($parameters['mailingLists'])) {
                    $mailinglists = $this->mailingListManager->getRepository()->findByIds($parameters['mailingLists']);
                }

            } elseif ($value instanceof EmailValue) {
                $email = $value->getValue();
            }
        }

        if ($email && $mailinglists) {
            foreach ($mailinglists as $mailinglist) {
                $subscription = new Subscription();
                $subscription->setEmail($email);
                $subscription->setMailingList($mailinglist);

                $this->subscriptionManager->save($subscription);
            }
        }
    }
}
