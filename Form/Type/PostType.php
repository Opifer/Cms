<?php

namespace Opifer\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

class PostType extends AbstractType
{
    /** @var RouterInterface */
    protected $router;

    /**
     * Constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction($this->router->generate('opifer_form_form_submit', ['id' => $options['form_id']]));
        $builder->add('valueset', 'opifer_valueset');
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['form_id']);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_form_post';
    }
}
