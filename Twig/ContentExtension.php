<?php

namespace Opifer\ContentBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

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

    /**
     * Constructor
     *
     * @param Twig_Environment   $twig
     * @param FragmentHandler    $fragmentHandler
     * @param ContentManager     $contentManager
     * @param ContainerInterface $container
     */
    public function __construct(\Twig_Environment $twig, FragmentHandler $fragmentHandler, ContentManager $contentManager, ContainerInterface $container)
    {
        $this->twig = $twig;
        $this->fragmentHandler = $fragmentHandler;
        $this->contentManager = $contentManager;
        $this->container = $container;
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
            new \Twig_SimpleFunction('render_content', [$this, 'renderContent'], [
                'is_safe' => array('html')
            ]),
            new \Twig_SimpleFunction('render_content_by_id', [$this, 'renderContentById'], [
                'is_safe' => array('html')
            ]),
            new \Twig_SimpleFunction('content_picker', [$this, 'contentPicker'], [
                'is_safe' => array('html')
            ]),
            new \Twig_SimpleFunction('nested_content', [$this, 'renderNestedContent'], [
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
        if (isset($arguments['block_mode'])) {
            $this->blockMode = $arguments['block_mode'];
        }

        $service = $this->container->get('opifer.content.block_manager')->getService($block);

        if ($this->blockMode && $this->blockMode === 'manage') {
            return $service->manage($block)->getContent();
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
    public function renderPlaceholder($context, $key)
    {
        $content = '';

        if (isset($context['block'])) {
            $container = $context['block'];
            foreach ($container->getChildren() as $block) {
                if ($block->getPosition() === ''.$key || ($key.'' === '0' && $block->getPosition() === '')) {
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

    /**
     * Render a content item by its slug or passed content object
     *
     * @return string
     */
    public function renderContent($contentItem)
    {
        $string = '';

        if($contentItem === false) {
            return $string;
        }

        $content = ($contentItem instanceof ContentInterface) ? $contentItem : $this->getContent($contentItem);

        $action = new ControllerReference('OpiferContentBundle:Frontend/Content:view', ['content' => $content]);
        $string = $this->fragmentHandler->render($action);

        return $string;
    }

    /**
     * Render a content item by its slug
     *
     * @return string
     */
    public function renderContentById($id)
    {
        $content = $this->getContentById($id);

        $action = new ControllerReference('OpiferContentBundle:Frontend/Content:view', ['content' => $content]);
        $string = $this->fragmentHandler->render($action);

        return $string;
    }

    /**
     * Render nested content
     *
     * @param ArrayCollection $values
     *
     * @return string
     */
    public function renderNestedContent($values)
    {
        $view = '';

        $contents = $this->contentManager->getRepository()->findByIds($values);
        foreach ($contents as $content) {
            $action = new ControllerReference('OpiferContentBundle:Frontend/Content:nested', ['content' => $content]);
            $view .= $this->fragmentHandler->render($action);
        }

        return $view;
    }

    /**
     * Get the view for the placeholder
     *
     * @param array  $context Passed automatically, when needs_context is set to TRUE
     * @param string $key
     *
     * @return string
     */
    public function getPlaceholder($context, $key)
    {
        if (!array_key_exists('layout', $context)) {
            return;
        }

        $layouts = $context['layout']->getLayoutsAt($key);

        if (!$layouts) {
            return;
        }

        $content = '';
        foreach ($layouts as $sublayout) {
            $context['layout'] = $sublayout;

            // If the sublayout has content, replace the context's content with
            // the sublayout's content
            if ($sublayout->getContent()) {
                $layoutContent = $this->contentManager->getRepository()
                    ->find($sublayout->getContent());

                $context['content'] = $layoutContent;
            }

            // If the sublayout has parameters, set the parameter data to the context
            if ($sublayout->getParameters()) {
                $context['parameters'] = $sublayout->getParameters();
            }

            // If the sublayout has an action, call the controller action before rendering.
            // Else, just render the template directly
            if ($sublayout->getAction()) {
                $action = new ControllerReference($sublayout->getAction(), $context, []);
                $content .= $this->fragmentHandler->render($action);
            } else {
                $content .= $this->twig->render($sublayout->getFilename(), $context);
            }
        }

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
