<?php

namespace Opifer\ContentBundle\ValueProvider;

use Opifer\ContentBundle\Form\Type\ContentListPickerType;
use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

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
        $builder->add('sort', HiddenType::class);
        $builder->add('content', ContentListPickerType::class, [
            'label' => $options['attribute']->getDisplayName()
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
