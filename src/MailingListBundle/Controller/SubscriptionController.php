<?php

namespace Opifer\MailingListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SubscriptionController extends Controller
{
    public function indexAction()
    {
        return $this->render('OpiferMailingListBundle:Subscription:index.html.twig');
    }
}
