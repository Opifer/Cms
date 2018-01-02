<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Entity\MediaValue;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\FormBuilderInterface;

class MediaValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /** @var string */
    protected $mediaClass;

    /**
     * Constructor
     *
     * @param string $mediaClass
     *
     * @throws \Exception when the passed mediaClass does not implement the MediaInterface
     */
    public function __construct($mediaClass)
    {
        if ($mediaClass != '' && !is_subclass_of($mediaClass, 'Opifer\EavBundle\Model\MediaInterface')) {
            throw new \Exception($mediaClass.' must implement Opifer\EavBundle\Model\MediaInterface');
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
            'label'    => $options['attribute']->getDisplayName(),
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
