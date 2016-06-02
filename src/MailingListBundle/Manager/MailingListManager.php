<?php

namespace Opifer\MailingListBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Opifer\MailingListBundle\Entity\MailingList;
use Opifer\MailingListBundle\Repository\MailingListRepository;

class MailingListManager
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return MailingList::class;
    }

    /**
     * @return MailingListRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getClass());
    }

    public function getManager()
    {
        return $this->em;
    }
}
