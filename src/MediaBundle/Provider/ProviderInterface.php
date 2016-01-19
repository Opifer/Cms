<?php

namespace Opifer\MediaBundle\Provider;

use Symfony\Component\Form\FormBuilderInterface;

use Opifer\MediaBundle\Model\MediaInterface;

/**
 * Provider Interface
 *
 * Simply extend from the AbstractProvider to implement most of the required
 * methods documented in this interface.
 */
interface ProviderInterface
{
    /**
     * The provider name
     *
     * @return string
     */
    public function getName();

    /**
     * A more humanly readable name
     *
     * @return string
     */
    public function getLabel();

    /**
     * Return the thumbnail of the media item
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function getThumb(MediaInterface $media);

    /**
     * The 'create new' form
     *
     * Symfony Form Documentation:
     * http://symfony.com/doc/current/book/forms.html
     * http://symfony.com/doc/current/reference/forms/types.html
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildCreateForm(FormBuilderInterface $builder, array $options);

    /**
     * The 'edit' form
     *
     * By default, when extending AbstractProvider, this redirects to the
     * newForm() method.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildEditForm(FormBuilderInterface $builder, array $options);

    /**
     * the view to be used for the single media partial on the index view
     *
     * @return string
     */
    public function singleView();

    /**
     * @param MediaInterface $media
     */
    public function postLoad(MediaInterface $media);

    /**
     * perform any prePersist actions
     *
     * @param MediaInterface $media
     */
    public function prePersist(MediaInterface $media);

    /**
     * perform any postPersist actions
     *
     * @param MediaInterface $media
     */
    public function postPersist(MediaInterface $media);

    /**
     * perform any preUpdate actions
     *
     * @param MediaInterface $media
     */
    public function preUpdate(MediaInterface $media);

    /**
     * perform any postUpdate actions
     *
     * @param MediaInterface $media
     */
    public function postUpdate(MediaInterface $media);

    /**
     * perform any preRemove actions
     *
     * @param MediaInterface $media
     */
    public function preRemove(MediaInterface $media);

    /**
     * perform any postRemove actions
     *
     * @param MediaInterface $media
     */
    public function postRemove(MediaInterface $media);
}
