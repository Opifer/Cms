<?php

namespace Opifer\ContentBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Opifer\ContentBundle\Model\ContentManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Multi Content Picker Form Type
 */
class ContentListPickerType extends AbstractType
{
    /** @var ContentManager */
    protected $contentManager;

    /**
     * Constructor
     *
     * @param ContentManager $contentManager
     */
    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    /**
     * Builds the form and transforms the model data.
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($original) {
                $ids = json_decode($original);

                $items = $this->contentManager->getRepository()
                    ->createQueryBuilder('c')
                    ->where('c.id IN (:ids)')
                    ->setParameter('ids', $ids)
                    ->getQuery()
                    ->getResult();

                return $items;
            },
            function ($submitted) {
                if (!$submitted instanceof ArrayCollection && !is_array($submitted)) {
                    return null;
                }

                $ids = [];
                foreach ($submitted as $content) {
                    $ids[] = $content->getId();
                }

                return json_encode($ids);
            }
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => true,
            'property' => 'title',
            'class'    => $this->contentManager->getClass(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return EntityType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'content_list_picker';
    }
}
