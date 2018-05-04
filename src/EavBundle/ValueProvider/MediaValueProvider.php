<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Entity\MediaValue;
use Opifer\EavBundle\Model\MediaInterface;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * TODO Move to CmsBundle
 */
class MediaValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /** @var string */
    protected $mediaClass;

    /**
     * Constructor
     *
     * @param string $mediaClass
     *
     * @throws \Exception If $mediaClass is not a subclass of MediaInterface
     */
    public function __construct($mediaClass)
    {
        if ($mediaClass != '' && !is_subclass_of($mediaClass, MediaInterface::class)) {
            throw new \Exception($mediaClass.' must implement '.MediaInterface::class);
        }

        if ($mediaClass == '') {
            $this->enabled = false;
        }

        $this->mediaClass = $mediaClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('medias', MediaPickerType::class, [
            'label' => $options['attribute']->getDisplayName(),
            'multiple' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return MediaValue::class;
    }
}
