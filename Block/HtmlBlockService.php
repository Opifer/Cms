<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Entity\HtmlBlock;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Class HtmlBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class HtmlBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected $view = 'OpiferContentBundle:Block:Content/html.html.twig';

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
            $builder->create('default', 'form', ['inherit_data' => true])
                    ->add('value', 'ckeditor', ['label' => 'label.rich_text', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
        )->add(
            $builder->create('properties', 'form')
                ->add('id', 'text', ['attr' => ['help_text' => 'help.html_id']])
                ->add('extra_classes', 'text', ['attr' => ['help_text' => 'help.extra_classes']])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new HtmlBlock;
    }
}