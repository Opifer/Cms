<?php

namespace Opifer\FormBundle\Model;

use Doctrine\ORM\EntityManagerInterface;

class PostManager
{
    /** @var EntityManager */
    protected $em;

    /** @var string */
    protected $class;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     * @param string                 $class
     */
    public function __construct(EntityManagerInterface $em, $class)
    {
        if (!is_subclass_of($class, 'Opifer\FormBundle\Model\PostInterface')) {
            throw new \Exception($class.' must extend Opifer\FormBundle\Model\PostInterface');
        }

        $this->em = $em;
        $this->class = $class;
    }

    /**
     * Get class.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Create a new attribute instance.
     *
     * @return Post
     */
    public function create()
    {
        $class = $this->getClass();
        $form = new $class();

        return $form;
    }

    /**
     * Save attribute.
     *
     * @param Post $form
     *
     * @return Post
     */
    public function save(Post $form)
    {
        $this->em->persist($form);
        $this->em->flush();

        return $form;
    }

    /**
     * Get repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getClass());
    }
}
