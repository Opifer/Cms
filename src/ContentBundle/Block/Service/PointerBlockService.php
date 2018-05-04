<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\ORM\EntityRepository;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\PointerBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PointerBlockService.
 */
class PointerBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var BlockManager */
    protected $blockManager;

    /**
     * Make sure ESI is always enabled on PointerBlock's to avoid having to publish each content item
     *
     * @var bool
     */
    protected $esiEnabled = true;

    /**
     * @param BlockRenderer $blockRenderer
     * @param BlockManager  $blockManager
     * @param array         $config
     */
    public function __construct(BlockRenderer $blockRenderer, BlockManager $blockManager, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->blockManager = $blockManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewParameters(BlockInterface $block)
    {
        $parameters = parent::getViewParameters($block);

        $reference = null;
        if ($block->getReference()) {
            $service = $this->getReferenceService($block);
            $reference = $block->getReference();
            $service->load($reference);
            $parameters = $service->getViewParameters($reference);
            $parameters['pointer'] = $block;
        }

        return $parameters;
    }

    public function getReferenceService(BlockInterface $block)
    {
        return $this->blockManager->getService($block->getReference()->getBlockType());
    }

    /**
     * {@inheritdoc}
     */
    public function getView(BlockInterface $block)
    {
        if (!$block->getReference()) {
            return $this->config['view'];
        }

        return $this->getReferenceService($block)->getView($block->getReference());
    }

    /**
     * {@inheritdoc}
     */
    public function getName(BlockInterface $block = null)
    {
        if (!$block || !$block->getReference()) {
            return 'Shared';
        }

        return sprintf('%s (shared)', $this->getReferenceService($block)->getName($block->getReference()));
    }

    public function allowShare(BlockInterface $block)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add($builder->create('default', FormType::class, ['inherit_data' => true])
            ->add('reference', EntityType::class, [
                'required' => false,
                'label' => 'label.block',
                'class' => 'OpiferContentBundle:Block',
                'choice_label' => 'sharedDisplayName', // Assuming that the entity has a "name" property
                'query_builder' => function (EntityRepository $blockRepository) {
                    return $blockRepository->createQueryBuilder('b')
                        ->add('orderBy', 'b.sharedDisplayName ASC')
                        ->andWhere('b.shared = :shared')
                        ->andWhere('b.content IS NULL')
                        ->andWhere('b.template IS NULL')
                        ->setParameter('shared', true);
                },
            ])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getManageFormTypeName()
    {
        return 'pointer';
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new PointerBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        if (is_null($block) || !$block->getReference()) {
            $tool = new Tool('Shared block', 'pointer');

            $tool->setIcon('all_inclusive')
                ->setDescription('This block will load a shared block');

            return $tool;
        }

        return $this->getReferenceService($block)->getTool($block->getReference());
    }

    /**
     * Sets the response headers defined on the reference service
     *
     * {@inheritdoc}
     */
    protected function setResponseHeaders(BlockInterface $block, Response $response)
    {
        if ($block && $block->getReference()) {
            if ($this->getReferenceService($block)->isEsiEnabled($block->getReference())) {
                $this->getReferenceService($block)->setResponseHeaders($block->getReference(), $response);
            } else {
                $response->setLastModified($block->getReference()->getUpdatedAt());
                $response->setPublic();
            }
        }
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'This block will load a shared block';
    }
}
