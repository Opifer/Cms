<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Entity\ImageBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ImageBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class ImageBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected $view = 'OpiferContentBundle:Block:image.html.twig';

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Image';
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('default', 'form', ['virtual' => true])
                ->add('image', 'mediapicker', [
                    'required'  => false,
                    'multiple' => false,
                    'property' => 'name',
                    'class' => 'OpiferCmsBundle:Media',
                ])
        );
    }


    /**
     * {@inheritDoc}
     */
    public function getManageFormTypeName()
    {
        return 'block_image';
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new ImageBlock;
    }

}