<?php

namespace Opifer\CmsBundle\Twig\Extension;

use Symfony\Component\Form\FormView;
use Symfony\Bridge\Twig\Form\TwigRendererInterface;

class FormExtension extends \Twig_Extension
{

    /** @var TwigRendererInterface */
    public $renderer;

    /**
     * @param TwigRendererInterface $renderer
     */
    public function __construct(TwigRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'opifer_autocomplete' => new \Twig_Function_Method($this, 'renderJavascript', array('is_safe' => array('html'))),
        );
    }

    /**
     * Render Function Form Javascript
     *
     * @param FormView $view
     *
     * @return string
     */
    public function renderJavascript(FormView $view)
    {
        return $this->renderer->searchAndRenderBlock($view, 'javascript');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'opifer.cms.twig.form_extension';
    }
}
