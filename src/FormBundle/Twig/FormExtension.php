<?php

namespace Opifer\FormBundle\Twig;

use Opifer\EavBundle\Manager\EavManager;
use Opifer\FormBundle\Model\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;

class FormExtension extends \Twig_Extension
{
    /** @var EavManager */
    protected $eavManager;

    /** @var FormFactoryInterface */
    protected $formFactory;

    /**
     * Constructor.
     *
     * @param EavManager           $eavManager
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EavManager $eavManager, FormFactoryInterface $formFactory)
    {
        $this->eavManager = $eavManager;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('create_form_view', [$this, 'createFormView']),
        ];
    }

    /**
     * Builds a Symfony form from the passed Form entity and returns the related FormView,
     * so we can use our Form as a standard Symfony form in our templates.
     *
     * @param FormInterface $form
     *
     * @return \Symfony\Component\Form\FormView
     */
    public function createFormView(FormInterface $form)
    {
        $post = $this->eavManager->initializeEntity($form->getSchema());

        $form = $this->formFactory->create('opifer_form_post', $post, ['form_id' => $form->getId()]);

        return $form->createView();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'opifer_form_extension';
    }
}
