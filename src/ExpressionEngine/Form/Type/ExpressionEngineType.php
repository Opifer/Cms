<?php

namespace Opifer\ExpressionEngine\Form\Type;

use Opifer\ExpressionEngine\ExpressionEngine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpressionEngineType extends AbstractType
{
    /** @var ExpressionEngine */
    protected $expressionEngine;

    /**
     * Constructor
     *
     * @param ExpressionEngine $expressionEngine
     */
    public function __construct(ExpressionEngine $expressionEngine)
    {
        $this->expressionEngine = $expressionEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['prototypes'] = $this->expressionEngine->serialize($options['prototypes']);
        $view->vars['debug'] = $options['debug'];

        if ($view->vars['value'] == '') {
            $view->vars['value'] = '[]';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'debug' => false
        ]);

        $resolver->setRequired('prototypes');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_expression_engine';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }
}
