<?php

namespace Opifer\FormBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Opifer\EavBundle\Model\SchemaManager;
use Opifer\FormBundle\Form\Type\PostType;
use Symfony\Component\Form\FormFactory;

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
     * @param FormFactory            $formFactory
     * @param SchemaManager          $schemaManager
     * @param PostManager            $postManager
     * @param string                 $class
     *
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $em, FormFactory $formFactory, SchemaManager $schemaManager, PostManager $postManager, $class)
    {
        if (!is_subclass_of($class, 'Opifer\FormBundle\Model\FormInterface')) {
            throw new \Exception(sprintf('%s must implement Opifer\FormBundle\Model\FormInterface', $class));
        }

        $this->em = $em;
        $this->formFactory = $formFactory;
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
     * Create a Symfony Form instance
     *
     * @param FormInterface $form
     * @param PostInterface $post
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createForm(FormInterface $form, PostInterface $post)
    {
        return $this->formFactory->create(PostType::class, $post, ['form_id' => $form->getId(), 'label' => false]);
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
