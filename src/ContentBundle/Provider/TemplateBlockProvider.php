<?php

namespace Opifer\ContentBundle\Provider;

use Doctrine\ORM\EntityManagerInterface;

class TemplateBlockProvider implements BlockProviderInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    public function getBlockOwner($id)
    {
        return $this->em->getRepository('OpiferContentBundle:Template')->find($id);
    }


}