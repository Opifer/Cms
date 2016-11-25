<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Model\Content;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Opifer\ContentBundle\Entity\DataViewBlock;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * DataView Block Service
 */
class DataViewBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * @param BlockRenderer $blockRenderer
     * @param ContentManagerInterface $contentManager
     */
    public function __construct(BlockRenderer $blockRenderer, EntityManagerInterface $em, array $config)
    {
        parent::__construct($blockRenderer, $config);
        
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock($args)
    {
        $block = new DataViewBlock();

        $dataView = $this->em->getRepository('OpiferContentBundle:DataView')->find($args['dataViewId']);
        $block->setDataView($dataView);

        return $block;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        if ($block) {
            return $this->getDataViewTool($block->getDataView());
        }

        $dataViews = $this->em->getRepository('OpiferContentBundle:DataView')->findBy(['active' => true]);

        $tools = [];

        foreach ($dataViews as $dataView) {
            $tool = $this->getDataViewTool($dataView);

            array_push($tools, $tool);
        }

        return $tools;
    }

    private function getDataViewTool($dataView)
    {
        $tool = new Tool($dataView->getDisplayName(), 'data_view');

        $tool->setData(['dataViewId' => $dataView->getId()])
            ->setIcon($dataView->getIconType())
            ->setGroup('Dataviews')
            ->setDescription($dataView->getDescription());

        return $tool;
    }
}
