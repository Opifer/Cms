<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Entity\BreadcrumbsBlock;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\Content;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Breadcrumbs Block Service.
 */
class BreadcrumbsBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    /**
     * @param BlockRenderer $blockRenderer
     * @param ContentManagerInterface $contentManager
     * @param array                   $config
     */
    public function __construct(BlockRenderer $blockRenderer, ContentManagerInterface $contentManager, array $config)
    {
        parent::__construct($blockRenderer, $config);
        $this->contentManager = $contentManager;
    }

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block' => $block,
        ];

        if ($this->environment->getObject() instanceof Content) {
            // Get current page slug to mark it as active when listing
            $parameters['content'] = $this->environment->getObject();
            $parameters['breadcrumbs'] = $parameters['content']->getBreadCrumbs();
        } else {
            $parameters['content'] = $this->contentManager->initialize();
            $parameters['content']->setSlug('example');
            $parameters['breadcrumbs'] = [
                'directory' => 'Example Directory',
                'example' => 'Example Page',
            ];
        }

        // Add homepage link as first breadcrumb if it does not exist in breadcrumbs
        if (!array_key_exists('index', $parameters['breadcrumbs'])) {
            $homePage = $this->contentManager->findOneBySlug('index');

            $parameters['breadcrumbs'] = array_merge(['index' => $homePage->getShortTitle()], $parameters['breadcrumbs']);
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new BreadcrumbsBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Breadcrumb', 'breadcrumbs');

        $tool->setIcon('linear_scale')
            ->setDescription('Navigation path of current content');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return mixed
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Navigation path of current content';
    }
}
