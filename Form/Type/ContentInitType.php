<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class ContentInitType extends AbstractType
{
    /** @var  TranslatorInterface */
    protected $translator;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var string */
    protected $templateClass;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     * @param RouterInterface     $router
     * @param array               $locales
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router, $templateClass)
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
