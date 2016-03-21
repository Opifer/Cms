<?php

namespace Opifer\ContentBundle\Twig;

use Opifer\ContentBundle\Block\BlockContainerInterface;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\BlockOwnerInterface;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\CompositeBlock;
use Opifer\ContentBundle\Entity\DocumentBlock;
use Opifer\ContentBundle\Entity\PointerBlock;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\ContentManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class ContentExtension extends \Twig_Extension
{
    /** @var \Twig_Environment */
    protected $twig;

    /** @var FragmentHandler */
    protected $fragmentHandler;

    /** @var \Opifer\ContentBundle\Block\Environment */
    protected $blockEnvironment;

    /** @var ContainerInterface */
    protected $container;

    /** @var RequestStack @var  */
    private $requestStack;

    /**
     * Constructor
     *
     * @param \Twig_Environment   $twig
     * @param FragmentHandler     $fragmentHandler
     * @param ContentManager      $contentManager
     * @param ContainerInterface  $container
     */
    public function __construct(\Twig_Environment $twig, FragmentHandler $fragmentHandler, ContainerInterface $container, RequestStack $requestStack)
    {
        $this->twig = $twig;
        $this->fragmentHandler = $fragmentHandler;
        $this->container = $container;
        $this->requestStack = $requestStack;

        if ($requestStack->getMasterRequest() !== null && $requestStack->getMasterRequest()->get('blockMode') === 'manage') {
            $this->blockMode = 'manage';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('render_block', [$this, 'renderBlock'], [
                'is_safe' => array('html')
            ]),
            new \Twig_SimpleFunction('render_placeholder', [$this, 'renderPlaceholder'], array(
                'is_safe' => array('html'),
                'needs_context' => true
            )),
            new \Twig_SimpleFunction('manage_tags', [$this, 'renderManageTags'], array(
                'is_safe' => array('html'),
                'needs_context' => true
            )),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests ()
    {
        return [
            new \Twig_SimpleTest('block_container', function (BlockInterface $block) { return $block instanceof BlockContainerInterface; }),
            new \Twig_SimpleTest('block_pointer', function (BlockInterface $block) { return $block instanceof PointerBlock; }),
        ];
    }


    /**
     * @param BlockInterface $block
     * @param array          $arguments
     *
     * @return string
     */
    public function renderBlock(BlockInterface $block, $arguments = array())
    {
        /** @var BlockManager $manager */
        $manager = $this->container->get('opifer.content.block_manager');

        $service = $manager->getService($block);

        if ($this->blockEnvironment->getBlockMode($block) == 'manage') {
            $content = $service->setEnvironment($this->blockEnvironment)->manage($block)->getContent();

            return $content;
        }

        return $service->execute($block)->getContent();
    }

    /**
     *
     *
     * @param $context
     * @param $key
     *
     * @return mixed
     */
    public function renderPlaceholder($context, $key = 0, $label = false)
    {
        if (isset($context['environment'])) {
            $this->blockEnvironment = $context['environment'];
        }

        $content = '';

        if ($this->blockEnvironment instanceof Environment) {
            /** @var BlockInterface $container */

            if (isset($context['block'])) {
                $container = $context['block'];
                if ($container instanceof CompositeBlock) {
                    $blocks = $this->blockEnvironment->getBlockChildren($container);
                } else {
                    throw new \Exception('Tried to render placeholder in a block which is not a CompositeBlock');
                }
            } else {
                $blocks = $this->blockEnvironment->getRootBlocks();
            }


            foreach ($blocks as $block) {
                if ($block->getPosition() === (int) $key || ((int) $key === 0 && $block->getPosition() === 0)) {
                    $content .= $this->renderBlock($block);
                }
            }

            if ($this->blockEnvironment->getBlockMode() === 'manage') {
                $content = $this->container->get('templating')->render('OpiferContentBundle:Block/Layout:placeholder.html.twig', ['content' => $content, 'key' => $key, 'id' => (isset($container)) ? $container->getId() : 0, 'manage_type' => 'placeholder']);
            }
        }

        return $content;
    }

    public function renderManageTags($context)
    {
        if (!$this->blockEnvironment || $this->blockEnvironment->getBlockMode() !== 'manage') {
            return;
        }

        $tags = '';

        if (isset($context['block'])) {
            /** @var Block $block */
            $block = $context['block'];
            $ownerId = ($block->getOwner()) ? $block->getOwner()->getId() : null;
            $tags .= sprintf(' data-pm-block-manage="true" data-pm-block-id="%d" data-pm-block-owner-id="%d" data-pm-block-type="%s"', $block->getId(), $ownerId, $context['manage_type']);
        } else if ($context['manage_type'] == 'placeholder')  {
            $tags .= sprintf(' data-pm-type="placeholder" data-pm-placeholder-key="%s" data-pm-placeholder-id="%s"', $context['key'], $context['id']);
        }

        if (isset($context['block_service'])) {
            $service = $context['block_service'];
            $tags .= sprintf(' data-pm-tool=\'%s\'', json_encode(array('icon' => $service->getTool()->getIcon())));
        }

        return $tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'opifer.content.twig.content_extension';
    }

    /**
     * @return \Opifer\ContentBundle\Block\Environment
     */
    public function getBlockEnvironment()
    {
        return $this->blockEnvironment;
    }

    /**
     * @param \Opifer\ContentBundle\Block\Environment $blockEnvironment
     */
    public function setBlockEnvironment($blockEnvironment)
    {
        $this->blockEnvironment = $blockEnvironment;
    }

}
