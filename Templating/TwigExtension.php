<?php

namespace Opifer\EavBundle\Templating;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormFactoryInterface;

use Opifer\EavBundle\Entity\FormValue;
use Opifer\EavBundle\Manager\EavManager;

class TwigExtension extends \Twig_Extension
{
    /** @var  \FormFactory */
    protected $formFactory;

    /** @var  \Doctrine\ORM\EntityManager */
    protected $em;

    /** @var \Opifer\EavBundle\Manager\EavManager */
    protected $eavManager;

    /**
     * Constructor
     *
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, EavManager $eavManager)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->eavManager = $eavManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('form_from_value', [$this, 'formFromValue'], [
                'is_safe' => ['html']
            ]),
        ];
    }

    /**
     * Get a form by a template
     *
     * @param string $template
     *
     * @return FormView
     */
    public function formFromValue(FormValue $value)
    {
        if (null === $template = $value->getTemplate()) {
            return false;
        }

        $entity = $this->eavManager->initializeEntity($template);

        $form = $this->formFactory->create('eav', $entity, [
            'valueId' => $value->getId()
        ]);

        return $form->createView();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'opifer.eav.twig_extension';
    }
}
