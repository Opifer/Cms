<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Opifer\ContentBundle\Form\DataTransformer\ArrayKeyTransformer;
use Opifer\ContentBundle\Form\DataTransformer\IdToEntityTransformer;

/**
 * Content picker form type
 */
class ContentPickerType extends AbstractType
{
    /** @var object */
    protected $contentManager;

    /**
     * Constructor
     *
     * @param object $contentManager
     */
    public function __construct($contentManager)
    {
        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ArrayKeyTransformer('content_id');
        $contentTransformer = new IdToEntityTransformer($this->contentManager);
        
        $builder->add(
            $builder->create('content_id', 'hidden')
                ->addModelTransformer($contentTransformer)
        );

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
