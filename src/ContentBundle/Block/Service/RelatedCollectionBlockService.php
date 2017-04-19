<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\ORM\EntityRepository;
use Opifer\CmsBundle\Entity\Attribute;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Entity\RelatedCollectionBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\EavBundle\Model\AttributeInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Related Collection Block Service.
 */
class RelatedCollectionBlockService extends CollectionBlockService
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('properties')
            ->add('related_attribute', ChoiceType::class, [
                'label' => 'Related on',
                'choices' => $this->getAttributes($options['data']->getOwner()),
                'choices_as_values' => true,
                'attr' => [
                    'help_text' => 'Define on what attribute the content should be related',
                    'tag' => 'general',
                ]
            ])
            // Remove some fields that were added in the CollectionBlockService but are not needed here
            ->remove('conditions')
            ->remove('filters')
            ->remove('filter_placement')
            ->remove('load_more')
        ;
    }

    /**
     * Get the selectable attributes
     *
     * @param ContentInterface $owner
     *
     * @return array
     */
    protected function getAttributes(ContentInterface $owner)
    {
        /** @var AttributeInterface $attributes */
        $attributes = $owner->getValueSet()->getAttributes();

        $choices = [];
        foreach ($attributes as $attribute) {
            if (!in_array($attribute->getValueType(), ['select', 'radio', 'checklist'])) {
                continue;
            }
            $choices[$attribute->getDisplayName()] = $attribute->getName();
        }

        return $choices;
    }

    /**
     * @return array
     */
    protected function getPrototypes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new RelatedCollectionBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Related Collection', 'related_collection');

        $tool->setIcon('query_builder')
            ->setDescription('Lists related content');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Lists related content items of the current page based on the defined conditions';
    }
}
