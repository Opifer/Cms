<?php

namespace Opifer\ContentBundle\ValueProvider;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;

class MultiContentValueProvider extends AbstractValueProvider implements ValueProviderInterface
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
        $builder->add('content', 'multicontentpicker', [
            'label'         => $options['attribute']->getDisplayName(),
            'multiple'      => true,   // Multiple selection allowed
            'property'      => 'title', // Assuming that the entity has a "name" property
            'class'         => $this->contentClass,
            'query_builder' => function (EntityRepository $contentRepository) {
                return $contentRepository->createQueryBuilder('c')->add('orderBy', 'c.title ASC');
            }
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\ContentBundle\Entity\MultiContentValue';
    }
}
