<?php

namespace Opifer\ContentBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;
use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;

class ContentListValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /** @var string */
    protected $contentClass;

    /**
     * Constructor
     *
     * @param string $contentClass
     */
    public function __construct($contentClass)
    {
        if ($contentClass != '' && !is_subclass_of($contentClass, 'Opifer\ContentBundle\Model\ContentInterface')) {
            throw new \Exception($contentClass.' must implement Opifer\ContentBundle\Model\ContentInterface');
        }

        if ($contentClass == '') {
            $this->enabled = false;
        }

        $this->contentClass = $contentClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sort', 'hidden');
        $builder->add('content', 'content_list_picker', [
            'label'    => $options['attribute']->getDisplayName(),
            'multiple' => true,
            'property' => 'title',
            'class'    => $this->contentClass,
            'data'     => $options['value']->getOrdered()
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\ContentBundle\Entity\ContentListValue';
    }
}
