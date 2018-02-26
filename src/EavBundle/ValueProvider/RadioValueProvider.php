<?php

namespace Opifer\EavBundle\ValueProvider;

use Doctrine\ORM\EntityRepository;
use Opifer\EavBundle\Form\Transformer\CollectionToObjectTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class RadioValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /** @var string */
    protected $optionClass;

    /**
     * Constructor
     *
     * @param string $optionClass
     */
    public function __construct($optionClass)
    {
        $this->optionClass = $optionClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributeId = $options['attribute']->getId();
        $transformer = new CollectionToObjectTransformer();
        
        $builder->add(
            $builder->create('options', EntityType::class, [
                'required'      => ($options['attribute']->getRequired()) ? true : false,
                'label'         => $options['attribute']->getDisplayName(),
                'multiple'      => false,   // Only allow single selection
                'expanded'      => true,   // Render as radio buttons
                'choice_label'  => 'displayName', // Assuming that the entity has a "name" property
                'class'         => $this->optionClass,
                'query_builder' => function (EntityRepository $optionRepository) use ($attributeId) {
                    return $optionRepository->createQueryBuilder('o')
                        ->add('orderBy', 'o.sort ASC')
                        ->innerJoin('o.attribute', 'a')
                        ->where('a.id = :attributeId')
                        ->setParameter('attributeId', $attributeId)
                    ;
                },
                'choice_attr' => function($option) {
                    // add description as attr on the choice
                    return ['description' => $option->getDescription()];
                },
            ])->addModelTransformer($transformer)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\RadioValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Radiobutton';
    }
}
