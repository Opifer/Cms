<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Opifer\ContentBundle\Form\DataTransformer\ArrayKeyTransformer;
use Opifer\ContentBundle\Form\DataTransformer\IdToContentTransformer;

/**
 * Content picker form type
 */
class ContentPickerType extends AbstractType
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    /**
     * Constructor
     *
     * @param ContentManagerInterface $contentManager
     */
    public function __construct(ContentManagerInterface $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ArrayKeyTransformer('content_id');
        $contentTransformer = new IdToContentTransformer($this->contentManager);

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
