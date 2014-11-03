<?php

namespace Opifer\EavBundle\ValueProvider;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

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
            throw new \Exception($mediaClass .' must implement Opifer\EavBundle\Model\MediaInterface');
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
        $builder->add('medias', 'mediapicker', [
            'label'         => $options['attribute']->getDisplayName(),
            'multiple'      => true,   // Multiple selection allowed
            'property'      => 'name', // Assuming that the entity has a "name" property
            'class'         => $this->mediaClass,
            'query_builder' => function (EntityRepository $mediaRepository) {
                return $mediaRepository->createQueryBuilder('m')->add('orderBy', 'm.name ASC');
            }
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
