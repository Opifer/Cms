<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Content picker form type.
 */
class ContentPickerType extends AbstractType
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    /**
     * Constructor.
     *
     * @param ContentManagerInterface $contentManager
     */
    public function __construct(ContentManagerInterface $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['as_object']) {
            $builder->addModelTransformer(new CallbackTransformer(
                function ($original) {
                    return $original;
                },
                function ($submitted) {
                    if (null == $submitted) {
                        return null;
                    }

                    $entity = $this->contentManager->getRepository()->find($submitted);

                    return $entity;
                }
            ));
        } else {
            $builder->addModelTransformer(new CallbackTransformer(
                function ($original) {
                    if (null == $original) {
                        return null;
                    }

                    $entity = $this->contentManager->getRepository()->find($original);

                    return $entity;
                },
                function ($submitted) {
                    return $submitted;
                }
            ));
        }
    }

    /**
     * Additionally to the default option, this type has an 'as_object' option, which defines to what type the
     * content item should be transformed. When as_object is true, the form will pass a Content object. If false,
     * only the ID will be passed.
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'as_object' => true,
            'compound' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'contentpicker';
    }
}
