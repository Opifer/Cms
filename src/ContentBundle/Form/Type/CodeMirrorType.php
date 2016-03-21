<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CodeMirrorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextareaType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_code_mirror';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $parameters = [
          'lineNumbers' => true,
        ];

        $view->vars['parameters'] = array_merge($parameters, $options['parameters']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'parameters' => array(
                'mode' => 'javascript',
            )
        ));
    }
}