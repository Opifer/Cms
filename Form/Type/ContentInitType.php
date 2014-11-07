<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Translation\LoggingTranslator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class ContentInitType extends AbstractType
{
    /** @var  Symfony\Bundle\FrameworkBundle\Translation\Translator */
    protected $translator;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var string */
    protected $templateClass;

    /**
     * Constructor
     *
     * @param Translator $translator
     * @param Router     $router
     * @param array      $locales
     */
    public function __construct(LoggingTranslator $translator, RouterInterface $router, $templateClass)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->templateClass = $templateClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('template', 'entity', [
                'class'    => $this->templateClass,
                'property' => 'name',
                'attr'     => [
                    'help_text' => $this->translator->trans('content.form.template.help_text')
                ]
            ])
            ->add('save', 'submit', [
                'label' => ucfirst($this->translator->trans('content.form.init.submit'))
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_content_init';
    }
}
