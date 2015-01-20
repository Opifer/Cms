<?php

namespace Opifer\EavBundle\Model;

use Doctrine\ORM\EntityManagerInterface;

class TemplateManager
{
    /** @var EntityManager */
    protected $em;

    /** @var string */
    protected $class;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $em
     * @param string                 $class
     */
    public function __construct(EntityManagerInterface $em, $class)
    {
        if (!is_subclass_of($class, 'Opifer\EavBundle\Model\TemplateInterface')) {
            throw new \Exception($class.' must implement Opifer\EavBundle\Model\TemplateInterface');
        }

        $this->em = $em;
        $this->class = $class;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Create a new template instance
     *
     * @return TemplateInterface
     */
    public function create()
    {
        $class = $this->getClass();
        $template = new $class();

        return $template;
    }

    /**
     * Save template
     *
     * @param TemplateInterface $template
     *
     * @return TemplateInterface
     */
    public function save(TemplateInterface $template)
    {
        $this->em->persist($template);
        $this->em->flush();

        return $template;
    }

    /**
     * Get repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getClass());
    }
}
