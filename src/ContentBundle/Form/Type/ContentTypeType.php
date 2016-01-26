<?php

namespace Opifer\ContentBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Opifer\EavBundle\Form\Type\SchemaType;

/**
 * ContentType Form type.
 */
class ContentTypeType extends AbstractType
{
    /** @var string */
    protected $contentClass;

    /**
     * Constructor.
     *
     * @param string $contentClass
     */
    public function __construct($contentClass)
    {
        $this->contentClass = $contentClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('schema', SchemaType::class, [
                'object_class' => $this->contentClass
            ])
        ;
    }
}
