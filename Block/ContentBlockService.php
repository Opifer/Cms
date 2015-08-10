<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Entity\ContentBlock;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Class ContentBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class ContentBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected $view = 'OpiferContentBundle:Block:content.html.twig';

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Content';
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
                    ->add('content', 'ckeditor')
            );
    }

    /**
     * {@inheritDoc}
     */
    public function getManageFormTypeName()
    {
        return 'block_content';
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new ContentBlock;
    }
}