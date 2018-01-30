<?php

namespace Opifer\ContentBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Entity\Block;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Block picker form type.
 */
class BlockPickerType extends AbstractType
{
    /** @var BlockManager */
    protected $blockManager;

    /**
     * Constructor.
     *
     * @param BlockManager $blockManager
     */
    public function __construct(BlockManager $blockManager)
    {
        $this->blockManager = $blockManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($original) {
                if (null == $original) {
                    return null;
                }

                $entity = $this->blockManager->getRepository()->find($original);

                return $entity;
            },
            function ($submitted) {
                if (null == $submitted) {
                    return null;
                }

                return $submitted->getId();
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Block::class,
            'choice_label' => 'name',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('b')
                    ->where('b.name IS NOT NULL')
                    // TODO: Add an option to specify the limitation of Block types
                    ->andWhere('b INSTANCE OF OpiferContentBundle:ModalBlock')
                    ->orderBy('b.name', 'ASC');
            },
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
