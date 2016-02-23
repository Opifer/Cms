<?php

namespace Opifer\ContentBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Opifer\ContentBundle\Model\ContentManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                return $original;
            },
            function ($submitted) {
                $array = json_decode($submitted, true);

                $ids = [];
                foreach ($array as $item) {
                    $ids[] = $item['id'];
                }

                return json_encode($ids);
            }
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'content_list_picker';
    }
}
