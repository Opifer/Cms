<?php

namespace Opifer\CmsBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Opifer\CmsBundle\Entity\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UsernameToUserTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (User) to a string (email).
     *
     * @param User|null $user
     *
     * @return string
     */
    public function transform($user)
    {
        if (null === $user) {
            return "";
        }

        return $user->getUsername();
    }

    /**
     * Transforms a string (email) to an object (User).
     *
     * @param string $username
     *
     * @return User|null
     *
     * @throws TransformationFailedException if object (User) is not found.
     */
    public function reverseTransform($username)
    {
        if (!$username) {
            return null;
        }

        $issue = $this->om
            ->getRepository('OpiferCmsBundle:User')
            ->findOneBy(['username' => $username]);

        if (null === $issue) {
            throw new TransformationFailedException(sprintf('A user with email "%s" does not exist!', $username));
        }

        return $issue;
    }
}
