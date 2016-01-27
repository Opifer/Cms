<?php

namespace Opifer\MediaBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Opifer\MediaBundle\Provider\Pool;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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

    /** @var string */
    protected $mediaClass;

    /**
     * Constructor.
     *
     * @param Pool   $providerPool
     * @param string $mediaClass
     */
    public function __construct(Pool $providerPool, $mediaClass)
    {
        $this->providerPool = $providerPool;
        $this->mediaClass = $mediaClass;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'property' => 'name',
            'class' => $this->mediaClass,
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
     *
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mediapicker';
    }
}
