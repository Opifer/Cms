<?php

namespace Opifer\EavBundle\ValueProvider;

use Doctrine\ORM\EntityRepository;
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
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('medias', MediaPickerType::class, [
            'label'         => $options['attribute']->getDisplayName(),
            'multiple'      => true,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\MediaValue';
    }
}
