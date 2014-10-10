<?php

namespace Opifer\MediaBundle\Provider;

use Symfony\Component\Form\FormBuilderInterface;

use Opifer\MediaBundle\Entity\Media;

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
     * The 'create new' form
     *
     * Symfony Form Documentation:
     * http://symfony.com/doc/current/book/forms.html
     * http://symfony.com/doc/current/reference/forms/types.html
     *
     * @param Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                       $options
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
     * @param Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                       $options
     *
     * @return void
     */
    public function buildEditForm(FormBuilderInterface $builder, array $options);

    /**
     * the view to be used for the partial on the index ciew
     *
     * @return string
     */
    public function indexView();

    /**
     * the view to be used for the create form
     *
     * @return string
     */
    public function newView();

    /**
     * the view to be used for the edit form
     *
     * @return string
     */
    public function editView();

    /**
     * perform any prePersist actions
     *
     * @param Media $media
     */
    public function prePersist(Media $media);

    /**
     * perform any postPersist actions
     *
     * @param Media $media
     */
    public function postPersist(Media $media);

    /**
     * perform any preUpdate actions
     *
     * @param Media $media
     */
    public function preUpdate(Media $media);

    /**
     * perform any postUpdate actions
     *
     * @param Media $media
     */
    public function postUpdate(Media $media);

    /**
     * perform any preRemove actions
     *
     * @param Media $media
     */
    public function preRemove(Media $media);

    /**
     * perform any postRemove actions
     *
     * @param Media $media
     */
    public function postRemove(Media $media);
}
