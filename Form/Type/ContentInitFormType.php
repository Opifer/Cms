<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ContentInitFormType
 *
 * @package Opifer\CmsBundle\Form\Type
 */
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
     * @param RouterInterface   $router
     * @param array             $locales
     */
    public function __construct(RouterInterface $router, $locales)
    {
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
                'attr'     => ['help_text' => 'content.form.site.help_text']
            ])
            ->add('schema', 'entity', [
                'class'    => 'OpiferEavBundle:Schema',
                'property' => 'name',
                'attr'     => ['help_text' => 'content.form.schema.help_text', ['%url%' => $this->router->generate('opifer.crud.new', ['slug' => 'schemas'])]]
            ])
            ->add('locale', 'locale', [
                'choices' => $this->locales,
                'attr'    => ['help_text' => 'content.form.locale.help_text']
            ])
            ->add('save', 'submit', [
                'label' => ucfirst('content.form.init.submit')
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_content_init_form';
    }
}
