<?php

namespace Opifer\EavBundle\ValueProvider;

use Doctrine\ORM\EntityRepository;
use Opifer\EavBundle\Form\Transformer\CollectionToObjectTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class SelectValueProvider extends AbstractValueProvider implements ValueProviderInterface
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
     * Select form
     *
     * Generates a select input that holds all attribute options
     *
     * This valuetype needs the CollectionToObjectTransformer, so we can set a single
     * relation on a one-to-many relation type.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributeId = $options['attribute']->getId();
        $transformer = new CollectionToObjectTransformer();

        $builder->add(
            $builder->create('options', EntityType::class, [
                'required'      => ($options['attribute']->getRequired()) ? true : false,
                'label'         => $options['attribute']->getDisplayName(),
                'placeholder'   => 'Select…',
                'multiple'      => false,
                'choice_label'  => 'displayName',
                'class'         => $this->optionClass,
                'query_builder' => function (EntityRepository $optionRepository) use ($attributeId) {
                    return $optionRepository->createQueryBuilder('o')
                        ->add('orderBy', 'o.sort ASC')
                        ->innerJoin('o.attribute', 'a')
                        ->where('a.id = :attributeId')
                        ->setParameter('attributeId', $attributeId);
                }
            ])->addModelTransformer($transformer)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\SelectValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Selectfield';
    }
}
