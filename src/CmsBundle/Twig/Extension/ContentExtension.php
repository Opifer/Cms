<?php

namespace Opifer\CmsBundle\Twig\Extension;

use Opifer\CmsBundle\Manager\ContentManager;
use Opifer\ContentBundle\Model\Content;
use Symfony\Component\Routing\RouterInterface;

class ContentExtension extends \Twig_Extension
{
    /** @var ContentManager */
    protected $contentManager;

    /** @var RouterInterface */
    protected $router;

    /**
     * Constructor.
     *
     * @param ContentManager $contentManager
     */
    public function __construct(ContentManager $contentManager, RouterInterface $router)
    {
        $this->contentManager = $contentManager;
        $this->router = $router;
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
     * @param string $string
     *
     * @return string
     */
    public function parseString($string)
    {
        $string = $this->replaceLinks($string);

        return $string;
    }

    /**
     * Replaces all links that matches the pattern with content urls.
     *
     * @param string $string
     *
     * @return string
     */
    protected function replaceLinks($string)
    {
        preg_match_all('/\[content_url\](.*?)\[\/content_url\]/', $string, $matches);

        if (!count($matches)) {
            return $string;
        }

        /** @var Content[] $contents */
        $contents = $this->contentManager->getRepository()->findByIds($matches[1]);

        foreach ($matches[0] as $key => $match) {
            if (isset($contents[$matches[1][$key]])) {
                $content = $contents[$matches[1][$key]];

                $url = $this->router->generate('_content', ['slug' => $content->getSlug()]);
            } else {
                $url = $this->router->generate('_content', ['slug' => '404']);
            }

            $string = str_replace($match, $url, $string);
        }

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'opifer.cms.twig.content_extension';
    }
}
