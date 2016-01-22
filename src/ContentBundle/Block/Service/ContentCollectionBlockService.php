<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Opifer\ContentBundle\Block\AbstractBlockService;
use Opifer\ContentBundle\Block\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\BlockContent;
use Opifer\ContentBundle\Entity\ContentCollectionBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Content Collection Block Service
 */
class ContentCollectionBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var EntityManager */
    protected $em;
    protected $view = 'OpiferContentBundle:Block:Content/image.html.twig';
    protected $originalCollection;

    /**
     * @param EngineInterface $templating
     * @param EntityManager   $em
     */
    public function __construct(EngineInterface $templating, EntityManager $em)
    {
        parent::__construct($templating);

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Content Collection';
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
                ->add('collection', 'content_list_picker', [
                    'label'    => 'collection',
                    'multiple' => true,
                    'property' => 'title',
                    'class'    => 'Opifer\CmsBundle\Entity\Content',
                    'data'     => $options['data']->getCollection()
                ])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new ContentCollectionBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new ContentTool('Content Collection', 'OpiferContentBundle:ContentCollectionBlock');

        $tool->setIcon('view_list')
            ->setDescription('Provides an image from the library in the right size.');

        return $tool;
    }

    /**
     * {@inheritdoc}
     */
    public function preFormSubmit(BlockInterface $block)
    {
        $this->originalCollection = new ArrayCollection();
        /** @var ContentCollectionBlock $block */
        foreach ($block->getCollection() as $blockContent) {
            $this->originalCollection->add($blockContent);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postFormSubmit(FormInterface $form, BlockInterface $block)
    {
        /** @var BlockContent $blockContent */
        foreach ($form->get('default')->get('collection')->getData() as $blockContent) {
            $blockContent->setBlock($block);
        }

        /** @var BlockContent $blockContent */
        foreach ($this->originalCollection as $blockContent) {
            /** @var ContentCollectionBlock $block */
            if (false === $block->getCollection()->contains($blockContent)) {
                $block->getCollection()->removeElement($blockContent);
                $this->em->remove($blockContent);
            }
        }
    }
}
