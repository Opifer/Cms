<?php

namespace Opifer\ContentBundle\Twig;

use Opifer\ContentBundle\Block\BlockContainerInterface;
use Opifer\ContentBundle\Block\BlockOwnerInterface;
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

    /** @var ContainerInterface */
    protected $container;

    /** @var RequestStack @var  */
    private $requestStack;

    /**
     * Constructor
     *
     * @param Twig_Environment   $twig
     * @param FragmentHandler    $fragmentHandler
     * @param ContentManager     $contentManager
     * @param ContainerInterface $container
     */
    public function __construct(\Twig_Environment $twig, FragmentHandler $fragmentHandler, ContentManager $contentManager, ContainerInterface $container, RequestStack $requestStack)
    {
        $this->twig = $twig;
        $this->fragmentHandler = $fragmentHandler;
        $this->contentManager = $contentManager;
        $this->container = $container;
        $this->requestStack = $requestStack;

        if ($requestStack->getMasterRequest()->get('blockMode') === 'manage') {
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
            new \Twig_SimpleFunction('get_content', [$this, 'getContent'], [
                'is_safe' => array('html')
            ]),
            new \Twig_SimpleFunction('get_content_by_id', [$this, 'getContentById'], [
                'is_safe' => array('html')
            ]),
            new \Twig_SimpleFunction('content_picker', [$this, 'contentPicker'], [
                'is_safe' => array('html')
            ]),
            new \Twig_SimpleFunction('breadcrumbs', [$this, 'getBreadcrumbs'], [
                'is_safe' => array('html')
            ]),
        ];
    }

    /**
     * Get a content item by its slug
     *
     * @return \Opifer\CmsBundle\Entity\Content
     */
    public function getContent($slug)
    {
        $content = $this->contentManager->getRepository()
            ->findOneBySlug($slug);

        return $content;
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

        if (isset($arguments['block_mode'])) {
            $this->blockMode = $arguments['block_mode'];
        }

        $service = $manager->getService($block);

        if ($this->blockMode && $this->blockMode === 'manage') {
            $content =  $service->manage($block)->getContent();
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
        if (isset($context['block_mode'])) {
            $this->blockMode = $context['block_mode'];
        }

        $content = '';

        if (isset($context['block'])) {
            $container = $context['block'];
            foreach ($container->getChildren() as $block) {
                if ($block->getPosition() === (int) $key || ((int) $key === 0 && $block->getPosition() === 0)) {
                    $content .= $this->renderBlock($block);
                }

                continue; // skip blocks that are not supposed to render at this placeholder key
            }
        }

        if ($this->blockMode && $this->blockMode === 'manage') {
            $content = $this->container->get('templating')->render('OpiferContentBundle:Block:manage.html.twig', ['content' => $content, 'key' => $key, 'manage_type' => 'placeholder']);
        }

        return $content;
    }

    /**
     * Get a content item by its id
     *
     * @return \Opifer\CmsBundle\Entity\Content
     */
    public function getContentById($id)
    {
        $content = $this->contentManager->getRepository()
            ->findOneById($id);

        return $content;
    }


    public function getBreadcrumbs(ContentInterface $content)
    {
        $return = [];
        $breadcrumbs = $content->getBreadcrumbs();

        if(sizeof($breadcrumbs) == 1 && key($breadcrumbs) == 'index') {
            return $return;
        }

        $index = 0;
        foreach ($breadcrumbs as $slug => $title) {
            if(substr($slug, -6) == '/index') {
                continue;
            }

            $indexSlug = (sizeof($breadcrumbs)-1 == $index) ? $slug : $slug.'/index';

            if($content = $this->contentManager->getRepository()->findOneBy(['slug' => $indexSlug])) {
                $return[$slug.'/'] = $content->getTitle();
            }

            $index++;
        }

        return $return;
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
