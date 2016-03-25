<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\DownloadsBlock;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Opifer\MediaBundle\Form\Type\MediaPickerType;

/**
 * Video Block Service
 */
class DownloadsBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('items', MediaPickerType::class, [
                    'required'  => false,
                    'multiple' => true,
                    'attr' => array('label_col' => 12, 'widget_col' => 12),
                ])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new DownloadsBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Downloads', 'downloads');

        $tool->setIcon('file_download')
            ->setDescription('Allows to download media items');

        return $tool;
    }
}
