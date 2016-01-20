<?php

namespace Opifer\FormBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Opifer\EavBundle\Model\SchemaManager;

class FormManager
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var SchemaManager */
    protected $schemaManager;

    /** @var string */
    protected $class;

    /** @var PostManager */
    protected $postManager;

    /**
     * @param EntityManagerInterface $em
     * @param SchemaManager          $schemaManager
     * @param PostManager            $postManager
     * @param string                 $class
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $em, SchemaManager $schemaManager, PostManager $postManager, $class)
    {
        if (!is_subclass_of($class, 'Opifer\FormBundle\Model\FormInterface')) {
            throw new \Exception(sprintf('%s must implement Opifer\FormBundle\Model\FormInterface', $class));
        }

        $this->em = $em;
        $this->schemaManager = $schemaManager;
        $this->postManager = $postManager;
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
     * Create a new form instance.
     *
     * @return Form
     */
    public function create()
    {
        $class = $this->getClass();
        $form = new $class();

        $schema = $this->schemaManager->create();
        $schema->setObjectClass($this->postManager->getClass());

        $form->setSchema($schema);

        return $form;
    }

    /**
     * Save attribute.
     *
     * @param Form $form
     *
     * @return Form
     */
    public function save(Form $form)
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
