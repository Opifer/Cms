<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\EavBundle\Model\AttributeInterface;
use Opifer\EavBundle\Model\AttributeManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filter Form Type.
 */
class FilterNameType extends AbstractType
{
    /** @var AttributeManager */
    protected $attributeManager;

    /**
     * Constructor
     *
     * @param AttributeManager $attributeManager
     */
    public function __construct(AttributeManager $attributeManager)
    {
        $this->attributeManager = $attributeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
        ]);
    }

    protected function getChoices()
    {
        $choices = [
            'Any field' => 'search',
        ];

        /** @var AttributeInterface $attribute */
        foreach ($this->attributeManager->getRepository()->findAll() as $attribute) {
            // Only list attributes with options for now
            if (!$attribute->hasOptions()) {
                continue;
            }

            $choices[$attribute->getDisplayName()] = sprintf('attribute.%s', $attribute->getName());
        }

        return $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
