<?php

namespace Opifer\MediaBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Opifer\MediaBundle\Model\Media;
use Opifer\MediaBundle\Model\MediaManager;
use Opifer\MediaBundle\Provider\Pool;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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

    /** @var array */
    protected $sortedIds;

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
            if ($options['multiple']) {
                $builder->addModelTransformer(new CallbackTransformer(
                    function ($original) {
                        $ids = json_decode($original, true);
                        $items = $this->mediaManager->getRepository()->findByIds($ids);

                        if (is_array($items)) {
                            uasort($items, function ($a, $b) use ($ids) {
                                return (array_search($a->getId(), $ids) > array_search($b->getId(), $ids));
                            });
                        }

                        return array_values($items);
                    },
                    function ($submitted) {
                        if ($submitted instanceof Media) {
                            $submitted = [$submitted];
                        } elseif (!is_array($submitted) && !$submitted instanceof ArrayCollection) {
                            $submitted = [];
                        }

                        $ids = [];
                        foreach ($submitted as $media) {
                            $ids[] = $media->getId();
                        }

                        return json_encode($ids);
                    }
                ));
            } else {
                $builder->addModelTransformer(new CallbackTransformer(
                    function ($original) {
                        if (null === $original || empty($original)) {
                            return null;
                        }

                        $item = $this->mediaManager->getRepository()->find($original);

                        if ($item) {
                            return $item;
                        }

                        return null;
                    },
                    function ($submitted) {
                        if (null === $submitted) {
                            return null;
                        }

                        return $submitted->getId();
                    }
                ));
            }

        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            if ($event->getData() && count($event->getData())) {
                $this->sortedIds = $event->getData();
            }
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (count($this->sortedIds) && is_array($data) && count($data)) {
                $ids = $this->sortedIds;
                uasort($data, function ($a, $b) use ($ids) {
                    return (array_search($a->getId(), $ids) > array_search($b->getId(), $ids));
                });

                $event->setData(array_values($data));
            }
        });
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
