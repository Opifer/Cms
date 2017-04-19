<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\ORM\EntityRepository;
use Opifer\CmsBundle\Entity\Attribute;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Entity\RelatedCollectionBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('related_attribute', EntityType::class, [
                'label' => 'Related on',
                'class' => Attribute::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('a')
                        ->leftJoin('a.values', 'v')
                        ->leftJoin('v.valueSet', 'vs')
                        ->where('a.valueType IN (:types)')
                        ->andWhere('vs.id = :valueSet')
                        ->setParameters([
                            'types' => ['select', 'radio', 'checklist'],
                            'valueSet' => $options['data']->getOwner()->getValueSet()->getId()
                        ]);
                },
                'choice_label' => 'displayName',
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
