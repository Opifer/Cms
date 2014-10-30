<?php

namespace Opifer\ContentBundle\Twig;

use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

use Opifer\ContentBundle\Model\ContentManager;

class ContentExtension extends \Twig_Extension
{
    /** @var \Twig_Environment */
    protected $twig;

    /** @var FragmentHandler */
    protected $fragmentHandler;

    /** @var ContentManager */
    protected $contentManager;

    /**
     * Constructor
     *
     * @param Twig_Environment $twig
     * @param FragmentHandler  $fragmentHandler
     * @param ContentManager   $contentManager
     */
    public function __construct(\Twig_Environment $twig, FragmentHandler $fragmentHandler, ContentManager $contentManager)
    {
        $this->twig = $twig;
        $this->fragmentHandler = $fragmentHandler;
        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('placeholder', [$this, 'getPlaceholder'], array(
                'is_safe' => array('html'),
                'needs_context' => true
            )),
            new \Twig_SimpleFunction('get_content', [$this, 'getContent'], [
                'is_safe' => array('html')
            ]),
            new \Twig_SimpleFunction('render_content', [$this, 'renderContent'], [
                'is_safe' => array('html')
            ]),
            new \Twig_SimpleFunction('nested_content', [$this, 'renderNestedContent'], [
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
     * Render a content item by its slug
     *
     * @return string
     */
    public function renderContent($slug)
    {
        $content = $this->getContent($slug);

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
        $content = '';
        foreach ($values as $value) {
            $action = new ControllerReference('OpiferSiteBundle:Content:nested', ['id' => $value]);
            $content .= $this->fragmentHandler->render($action);
        }

        return $content;
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'opifer.content.twig.content_extension';
    }
}
