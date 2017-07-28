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
        if ($options['as_collection']) {
            $builder->addModelTransformer(new CallbackTransformer(
                function ($original) {
                    $ids = [];
                    foreach ($original as $content) {
                        $ids[] = $content->getId();
                    }

                    return json_encode($ids);
                },
                function ($submitted) {
                    $array = json_decode($submitted, true);

                    $ids = [];
                    foreach ($array as $item) {
                        $ids[] = $item['id'];
                    }

                    return $this->contentManager->getRepository()->findByIds($ids);
                }
            ));
        } else {
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
    }

    /**
     * Additionally to the default option, this type has an 'as_collection' option, which defines to what type the
     * content item should be transformed. When as_collection is true, the form will pass a Content object. If false,
     * only the ID will be passed.
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'as_collection' => false,
        ]);
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
