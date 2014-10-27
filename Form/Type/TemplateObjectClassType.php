<?php
namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * ObjectClass form field for templates
 *
 * Gives the option to choose one of the available objectclasses for templates.
 */
class TemplateObjectClassType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'choices'  => [
                'Opifer\CmsBundle\Entity\Content' => 'Content',
                'Opifer\CmsBundle\Entity\Post'    => 'Post',
                'Opifer\CmsBundle\Entity\Layout'  => 'Layout'
            ]
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'template_object_class';
    }
}
