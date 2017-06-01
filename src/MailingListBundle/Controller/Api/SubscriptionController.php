<?php

namespace Opifer\MailingListBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Opifer\MailingListBundle\Entity\MailingList;
use Opifer\MailingListBundle\Entity\Subscription;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class SubscriptionController extends FOSRestController
{
    /**
     * @ApiDoc()
     * @Post("/subscription/{mailinglistId}")
     *
     * @ParamConverter("subscription", converter="fos_rest.request_body", options={"validator"})
     */
    public function postSubscribeAction($mailinglistId, Subscription $subscription, ConstraintViolationListInterface $validationErrors)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $mailinglist = $em->getRepository('OpiferMailingListBundle:MailingList')->find($mailinglistId);
        $subscription->setMailingList($mailinglist);

        $em->persist($subscription);
        $em->flush();

        return $subscription;
    }
}
