<?php

namespace Opifer\CmsBundle\Twig\Extension;

use Opifer\CmsBundle\Entity\AttachmentValue;
use Opifer\EavBundle\Entity\Value;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\FormView;

class FormExtension extends \Twig_Extension
{
    /** @var FormRendererInterface */
    public $renderer;

    /**
     * @param FormRendererInterface $renderer
     */
    public function __construct(FormRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [
            new \Twig_SimpleTest('attachment', function (Value $value) {
                return $value instanceof AttachmentValue;
            }),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('opifer_autocomplete', [$this, 'renderJavascript', ['is_safe' => ['html']]])
        ];
    }

    /**
     * Render Function Form Javascript.
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
