<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of PresentationEditorType
 *
 * @author dylan
 */
class PresentationEditorType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['compound' => false]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'presentationeditor';
    }
}
