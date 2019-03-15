<?php

namespace Opifer\EavBundle\ValueProvider;

use Doctrine\ORM\EntityRepository;
use Opifer\EavBundle\Entity\CheckListValue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class ChecklistValueProvider extends AbstractValueProvider implements ValueProviderInterface
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributeId = $options['attribute']->getId();
        $builder->add('options', EntityType::class, [
            'required'      => ($options['attribute']->getRequired()) ? true : false,
            'label'         => $options['attribute']->getDisplayName(),
            'multiple'      => true,   // Multiple selection allowed
            'expanded'      => true,   // Render as checkboxes
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
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return CheckListValue::class;
    }
}
