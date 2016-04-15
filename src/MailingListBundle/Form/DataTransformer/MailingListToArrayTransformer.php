<?php

namespace Opifer\MailingListBundle\Form\DataTransformer;

use Opifer\MailingListBundle\Entity\MailingList;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MailingListToArrayTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an array (mailinglists) to an array of ids.
     *
     * @param mailinglist|null $mailingList
     *
     * @return array
     */
    public function transform($mailingListIds)
    {
        // no mailinglist ids? It's optional, so that's ok
        if (!$mailingListIds || !count($mailingListIds)) {
            return;
        }

        $mailingLists = $this->manager
            ->getRepository('OpiferMailingListBundle:MailingList')
            // query for the mailinglist with this id
            ->findById($mailingListIds)
        ;

        if (null === $mailingLists) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'Mailinglists %s could not be fetched from database',
                implode(', ', $mailingListIds)
            ));
        }

        return $mailingLists;
    }

    /**
     * Transforms a array (ids) to an array (mailinglists).
     *
     * @param array $mailingListIds
     *
     * @return array|null
     *
     * @throws TransformationFailedException if object (mailinglist) is not found.
     */
    public function reverseTransform($mailingLists)
    {
        $mailingListIds = [];

        if (null === $mailingLists) {
            return $mailingListIds;
        }

        foreach ($mailingLists as $mailingList) {
            $mailingListIds[] = $mailingList->getId();
        };

        return $mailingListIds;
    }
}
