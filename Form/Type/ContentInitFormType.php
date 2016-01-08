<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Translation\LoggingTranslator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class ContentInitFormType extends AbstractType
{
    /** @var LoggingTranslator */
    protected $translator;

    /** @var RouterInterface */
    protected $router;

    /** @var array */
    protected $locales;

    /**
     * Constructor.
     *
     * @param LoggingTranslator $translator
     * @param RouterInterface   $router
     * @param array             $locales
     */
    public function __construct(LoggingTranslator $translator, RouterInterface $router, $locales)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', 'entity', [
                'class' => 'OpiferCmsBundle:Site',
                'property' => 'name',
                'attr' => ['help_text' => $this->translator->trans('content.form.site.help_text')],
            ])
            ->add('template', 'entity', [
                'class' => 'OpiferEavBundle:Template',
                'property' => 'name',
                'attr' => ['help_text' => $this->translator->trans('content.form.template.help_text', ['%url%' => $this->router->generate('opifer_cms_template_create')])],
            ])
            ->add('locale', 'locale', [
                'choices' => $this->locales,
                'attr' => ['help_text' => $this->translator->trans('content.form.locale.help_text')],
            ])
            ->add('save', 'submit', [
                'label' => ucfirst($this->translator->trans('content.form.init.submit')),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_content_init_form';
    }
}
