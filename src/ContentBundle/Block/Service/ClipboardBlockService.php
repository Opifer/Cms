<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\RecursiveBlockIterator;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\Content;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Clipboard Block Service
 */
class ClipboardBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var BlockManager */
    protected $blockManager;

    /** @var Session */
    protected $session;

    /** @var array */
    protected $blockIds;

    const SESSION_KEY = 'clipboard-blocks';

    public function __construct(BlockManager $blockManager, Session $session)
    {
        $this->blockManager = $blockManager;
        $this->session = $session;

        if ($session->has(self::SESSION_KEY)) {
            $this->blockIds = $session->get(self::SESSION_KEY);
        }
    }

    public function createBlock($args)
    {
        $block = $this->blockManager->find($args['id'], true);

        // Duplicate function likes to have arrays
        $blocks = $this->blockManager->duplicate(array($block), $args['owner']);

        $block = array_shift($blocks);

        return $block;
    }

    /**
     * @inheritDoc
     */
    public function getName(BlockInterface $block = null)
    {

    }

    /**
     * @inheritDoc
     */
    public function getView(BlockInterface $block)
    {
    }

    /**
     * @inheritDoc
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
    }

    /**
     * @inheritDoc
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * @inheritDoc
     */
    public function preFormSubmit(BlockInterface $block)
    {
    }

    /**
     * @inheritDoc
     */
    public function postFormSubmit(FormInterface $form, BlockInterface $block)
    {
    }


    public function addToClipboard(BlockInterface $block)
    {
        $this->blockIds[] = $block->getId();

        $this->session->set(self::SESSION_KEY, $this->blockIds);
    }

    protected function getClipboardBlocks()
    {
        if (is_array($this->blockIds) && count($this->blockIds)) {
            return $this->blockManager->findById($this->blockIds, true);
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function getTool(BlockInterface $block = null)
    {
        $tools = array();

        foreach ($this->getClipboardBlocks() as $block) {
            /** @var ToolsetMemberInterface $copyService */
            $copyService = $this->blockManager->getService($block);

            $copyTool = $copyService->getTool($block);


            $tool = new Tool($copyTool->getName(), 'clipboard');
            $tool->setGroup('Clipboard');
            $tool->setIcon($copyTool->getIcon());
            $tool->setData(['id' => $block->getId()]);
            $title = ($block->getOwner() instanceof Content) ? $block->getOwner()->getTitle() : $block->getOwner()->getName();
            $tool->setDescription(sprintf('<span class="material-icons md-18">swap_horz</span> %s', $title));

            $tools[] = $tool;
        }

        return $tools;
    }

}