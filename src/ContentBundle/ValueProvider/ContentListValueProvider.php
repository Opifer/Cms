<?php

namespace Opifer\ContentBundle\ValueProvider;

use Opifer\ContentBundle\Entity\ContentListValue;
use Opifer\ContentBundle\Form\Type\ContentListPickerType;
use Opifer\ContentBundle\Model\ContentInterface;
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
     *
     * @throws \Exception if content class does not implement ContentInterface
     */
    public function __construct($contentClass)
    {
        if ($contentClass != '' && !is_subclass_of($contentClass, ContentInterface::class)) {
            throw new \Exception($contentClass.' must implement '.ContentInterface::class);
        }

        if ($contentClass == '') {
            $this->enabled = false;
        }

        $this->contentClass = $contentClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sort', HiddenType::class);
        $builder->add('content', ContentListPickerType::class, [
            'label' => $options['attribute']->getDisplayName()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return ContentListValue::class;
    }
}
