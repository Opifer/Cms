<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Opifer\EavBundle\Form\Transformer\ArrayKeyTransformer;

/**
 * Nested content form type
 */
class ContentPickerType extends AbstractType
{
    /** @var RouterInterface */
    protected $router;

    /**
     * Constructor
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
        $transformer = new ArrayKeyTransformer('content_id');
        
        $builder
            ->add('content_id', 'hidden',[
                'label' => false,
            ])
        ;
        
        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'contentpicker';
    }
}
