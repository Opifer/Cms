<?php

namespace Opifer\MediaBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Opifer\MediaBundle\Model\MediaManager;
use Opifer\MediaBundle\Provider\Pool;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Media Picker Form Type.
 *
 * Renders a media picker field in a form
 */
class MediaPickerType extends AbstractType
{
    /** @var \Opifer\MediaBundle\Provider\Pool */
    protected $providerPool;

    /** @var MediaManager */
    protected $mediaManager;

    /**
     * Constructor.
     *
     * @param Pool   $providerPool
     * @param MediaManager $mediaManager
     */
    public function __construct(Pool $providerPool, MediaManager $mediaManager)
    {
        $this->providerPool = $providerPool;
        $this->mediaManager = $mediaManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['to_json']) {
            $builder->addModelTransformer(new CallbackTransformer(
                function ($original) {
                    $ids = json_decode($original, true);
                    return $this->mediaManager->getRepository()->findByIds($ids);
                },
                function ($submitted) {
                    $ids = [];
                    foreach ($submitted as $media) {
                        $ids[] = $media->getId();
                    }

                    return json_encode($ids);
                }
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'to_json' => false,
            'property' => 'name',
            'class' => $this->mediaManager->getClass(),
            'query_builder' => function (EntityRepository $mediaRepository) {
                return $mediaRepository->createQueryBuilder('m')->add('orderBy', 'm.name ASC');
            }
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'providers' => $this->providerPool->getProviders(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mediapicker';
    }
}
