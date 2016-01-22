<?php

namespace Opifer\ContentBundle\Twig;

use Opifer\ContentBundle\Block\BlockContainerInterface;
use Opifer\ContentBundle\Block\BlockOwnerInterface;
use Opifer\ContentBundle\Entity\CompositeBlock;
use Opifer\ContentBundle\Entity\DocumentBlock;
use Opifer\ContentBundle\Environment\Environment;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\HttpFoundation\RequestStack;

use Opifer\ContentBundle\Model\ContentManager;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\BlockInterface;

class ContentExtension extends \Twig_Extension
{
    /** @var \Twig_Environment */
    protected $twig;

    /** @var FragmentHandler */
    protected $fragmentHandler;

    /** @var ContentManager */
    protected $contentManager;

    /** @var string */
    protected $blockMode;

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
    public function __construct(\Twig_Environment $twig, FragmentHandler $fragmentHandler, ContentManager $contentManager, ContainerInterface $container, RequestStack $requestStack)
    {
        $this->twig = $twig;
        $this->fragmentHandler = $fragmentHandler;
        $this->contentManager = $contentManager;
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
        $manager = $this->container->get('opifer.content.block_manager');

        $service = $manager->getService($block);

        if ($this->blockEnvironment->getBlockMode($block) == 'manage') {
            $content =  $service->setEnvironment($this->blockEnvironment)->manage($block)->getContent();
//            $block->isRendered = true;
//
//            // TODO check for unrendered blocks
//            if ($block instanceof BlockContainerInterface) {
//                foreach ($block->getChildren() as $child) {
//                    if (!property_exists($child, 'isRendered') || $child->isRendered) {
//                        $content .= $manager->getService($child)->manage($child)->getContent();
//                    }
//                }
//            }

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
    public function renderPlaceholder($context, $key = 0)
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

//                continue; // skip blocks that are not supposed to render at this placeholder key
            }
        }

        if ($this->blockEnvironment->getBlockMode() === 'manage') { // && $this->blockEnvironment->getContent()->getBlock()->getId() == $block->getOwner()->getId()
            $content = $this->container->get('templating')->render('OpiferContentBundle:Block:manage.html.twig', ['content' => $content, 'key' => $key, 'manage_type' => 'placeholder']);
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'opifer.content.twig.content_extension';
    }

    /**
     * @return string
     */
    public function getBlockMode()
    {
        return $this->blockMode;
    }

    /**
     * @param string $blockMode
     */
    public function setBlockMode($blockMode)
    {
        $this->blockMode = $blockMode;
    }


}
