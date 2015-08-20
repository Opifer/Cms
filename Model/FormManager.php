<?php

namespace Opifer\FormBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Opifer\EavBundle\Model\TemplateManager;

class FormManager
{
    /** @var EntityManager */
    protected $em;

    /** @var TemplateManager */
    protected $templateManager;

    /** @var string */
    protected $class;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     * @param string                 $class
     */
    public function __construct(EntityManagerInterface $em, TemplateManager $templateManager, $class)
    {
        if (!is_subclass_of($class, 'Opifer\FormBundle\Model\FormInterface')) {
            throw new \Exception(sprintf('%s must implement Opifer\FormBundle\Model\FormInterface', $class));
        }

        $this->em = $em;
        $this->templateManager = $templateManager;
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

        $template = $this->templateManager->create();
        $form->setTemplate($template);

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
