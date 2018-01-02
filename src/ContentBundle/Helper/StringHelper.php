<?php

namespace Opifer\ContentBundle\Helper;

use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\ContentManager;
use Symfony\Component\Routing\RouterInterface;

class StringHelper
{
    protected $contentManager;

    protected $router;

    public function __construct(ContentManager $cm, RouterInterface $router)
    {
        $this->contentManager = $cm;
        $this->router = $router;
    }

    /**
     * Replaces all links that matches the pattern with content urls.
     *
     * @param string $string
     *
     * @return string
     */
    public function replaceLinks($string)
    {
        preg_match_all('/(\[content_url\](.*?)\[\/content_url\]|\[content_url\](.*?)\[\\\\\/content_url\])/', $string, $matches);

        if (!count($matches)) {
            return $string;
        }

        if (!empty($matches[3][0])) {
            $matches[1] = $matches[3];
        } elseif (!empty($matches[2][0])) {
            $matches[1] = $matches[2];
        }

        /** @var ContentInterface[] $contents */
        $contents = $this->contentManager->getRepository()->findByIds($matches[1]);
        $array = [];
        foreach ($contents as $content) {
            $array[$content->getId()] = $content;
        }

        foreach ($matches[0] as $key => $match) {
            if (isset($array[$matches[1][$key]])) {
                $content = $array[$matches[1][$key]];

                $url = $this->router->generate('_content', ['slug' => $content->getSlug()]);
            } else {
                $url = $this->router->generate('_content', ['slug' => '404']);
            }

            $string = str_replace($match, $url, $string);
        }

        return $string;
    }
}
