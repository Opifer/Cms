<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\CoockieWallBlock;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Opifer\CmsBundle\Entity\Form;
use Opifer\CmsBundle\Form\Type\CKEditorType;

/**
 * CoockieWall Block Service
 */
class CoockieWallBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('value', CKEditorType::class, [
                    'label' => 'Message',
                ])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new CoockieWallBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('CoockieWall', 'coockiewall');

        $tool->setIcon('text_fields')
            ->setDescription('Coockiewall');

        return $tool;
    }

    public function getView(BlockInterface $block)
    {
        return 'OpiferContentBundle:Block:Content/coockiewall.html.twig';
    }


}
