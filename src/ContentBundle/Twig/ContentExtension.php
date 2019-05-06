<?php

namespace Opifer\ContentBundle\Twig;

use Opifer\ContentBundle\Block\BlockContainerInterface;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\CompositeBlock;
use Opifer\ContentBundle\Entity\PointerBlock;
use Opifer\ContentBundle\Entity\Template;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\Content;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\ContentManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\Routing\RouterInterface;

class ContentExtension extends \Twig_Extension
{
    /** @var \Twig_Environment */
    protected $twig;

    /** @var FragmentHandler */
    protected $fragmentHandler;

    /** @var Environment */
    protected $blockEnvironment;

    /** @var ContainerInterface */
    protected $container;

    /** @var RequestStack */
    private $requestStack;

    /**
     * ContentExtension constructor.
     *
     * @param \Twig_Environment  $twig
     * @param FragmentHandler    $fragmentHandler
     * @param ContainerInterface $container
     * @param RequestStack       $requestStack
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
                'is_safe' => array('html'),
            ]),
            new \Twig_SimpleFunction('render_placeholder', [$this, 'renderPlaceholder'], array(
                'is_safe' => array('html'),
                'needs_context' => true,
            )),
            new \Twig_SimpleFunction('manage_tags', [$this, 'renderManageTags'], array(
                'is_safe' => array('html'),
                'needs_context' => true,
            )),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('parse', [$this, 'parseString']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [
            new \Twig_SimpleTest('block_container', function (BlockInterface $block) { return $block instanceof BlockContainerInterface; }),
            new \Twig_SimpleTest('block_pointer', function (BlockInterface $block) { return $block instanceof PointerBlock; }),
            new \Twig_SimpleTest('parent_of', [$this, 'isParentOf']),
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
        $service->setEnvironment($this->blockEnvironment);

        if ($this->blockEnvironment->getBlockMode($block) == 'manage') {
            $content = $service->manage($block)->getContent();

            return $content;
        }

        return $service->execute($block)->getContent();
    }

    /**
     * @param array  $context
     * @param int    $key
     * @param bool   $label
     * @param string $htmlTag
     * @param null   $data
     *
     * @return string
     * @throws \Exception
     */
    public function renderPlaceholder($context, $key = 0, $label = false, $htmlTag = 'div', $data = null)
    {
        if (isset($context['environment'])) {
            $this->blockEnvironment = $context['environment'];
        }

        $content = '';

        if ($this->blockEnvironment instanceof Environment) {
            if (isset($context['block'])) {
                /* @var BlockInterface $container */
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
                $data['class'] = 'pm-placeholder '.$data['class'];
            }

            $content = $this->container->get('templating')->render('OpiferContentBundle:Block/Layout:placeholder.html.twig', [
                'content' => $content,
                'tag' => $htmlTag,
                'data' => $data,
                'key' => $key,
                'id' => (isset($container)) ? $container->getId() : 0,
                'manage_type' => 'placeholder'
            ]);
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

            if (isset($context['pointer']) && $context['pointer'] instanceof PointerBlock) {
                $tags .= sprintf(' data-pm-block-manage="true" data-pm-block-id="%d" data-pm-block-owner-id="%d" data-pm-block-type="%s"', $context['pointer']->getId(), $ownerId, $context['manage_type']);
                $tags .= sprintf(' data-pm-block-pointer="true" data-pm-block-reference-id="%d"', $block->getId());
            } else {
                $tags .= sprintf(' data-pm-block-manage="true" data-pm-block-id="%d" data-pm-block-owner-id="%d" data-pm-block-type="%s"', $block->getId(), $ownerId, $context['manage_type']);
            }
        } elseif ($context['manage_type'] == 'placeholder') {
            $tags .= sprintf(' data-pm-type="placeholder" data-pm-placeholder-key="%s" data-pm-placeholder-id="%s"', $context['key'], $context['id']);
        }

        if (isset($context['block_service'])) {
            $service = $context['block_service'];
            $tags .= sprintf(' data-pm-tool=\'%s\'', json_encode(array('icon' => $service->getTool($block)->getIcon())));
        }

        return $tags;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function parseString($string)
    {

        $stringHelper = $this->container->get('opifer.content.string_helper');

        $string = $stringHelper->replaceLinks($string);

        return $string;
    }
    
    /**
     * @param string|ContentInterface $content
     * @param ContentInterface        $child
     *
     * @return bool
     */
    public function isParentOf($content, ContentInterface $child)
    {
        if ($child instanceof Template) {
            return false;
        }

        if (is_string($content)) {
            // Strip the dev front controller if its defined
            if (strpos($content, '/app_dev.php') !== false) {
                $content = substr($content, strlen('/app_dev.php'));
            }

            // Strip the first character if it's a slash
            if (substr($content, 0, 1) === '/') {
                $content = ltrim($content, '/');
            }

            if (substr($child->getSlug(), 0, strlen($content)) === $content) {
                return true;
            }
        } else {
            if ($child->getId() == $content->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'opifer.content.twig.content_extension';
    }

    /**
     * @return Environment
     */
    public function getBlockEnvironment()
    {
        return $this->blockEnvironment;
    }

    /**
     * @param Environment $blockEnvironment
     */
    public function setBlockEnvironment($blockEnvironment)
    {
        $this->blockEnvironment = $blockEnvironment;
    }

    /**
     * @return RouterInterface
     */
    protected function getRouter()
    {
        return $this->container->get('router');
    }

    /**
     * @return ContentManager
     */
    protected function getContentManager()
    {
        return $this->container->get('opifer.content.content_manager');
    }
}
