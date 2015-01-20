<?php

namespace Opifer\EavBundle\Templating;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

use Opifer\EavBundle\Entity\FormValue;
use Opifer\EavBundle\Manager\EavManager;

class TwigExtension extends \Twig_Extension
{
    /** @var  \FormFactory */
    protected $formFactory;

    /** @var \Opifer\EavBundle\Manager\EavManager */
    protected $eavManager;
    
    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $generator;

    /**
     * Constructor
     *
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory, EavManager $eavManager, RouterInterface $router)
    {
        $this->generator = $router;
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
            'action' => $this->generator->generate('opifer_eav_form_submit', ['valueId' => $value->getId()]),
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
