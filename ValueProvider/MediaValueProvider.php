<?php

namespace Opifer\EavBundle\ValueProvider;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class MediaValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('medias', 'mediapicker', [
            'label'         => $options['attribute']->getDisplayName(),
            'multiple'      => true,   // Multiple selection allowed
            'property'      => 'name', // Assuming that the entity has a "name" property
            'class'         => 'OpiferCmsBundle:Media',
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
