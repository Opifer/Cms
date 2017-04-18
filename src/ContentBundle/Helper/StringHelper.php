<?php

namespace Opifer\ContentBundle\Helper;


use Doctrine\ORM\EntityManager;
use Opifer\ContentBundle\Model\ContentManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StringHelper
{

    protected $contentManager;

    protected $container;

    public function __construct(ContentManager $cm, ContainerInterface $container)
    {
        $this->contentManager = $cm;
        $this->container = $container;
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

        if(!empty($matches[3])){
            $matches[1] = $matches[3];
        }

        /** @var Content[] $contents */
        $contents = $this->contentManager->getRepository()->findByIds($matches[1]);
        $array = [];
        foreach ($contents as $content) {
            $array[$content->getId()] = $content;
        }

        foreach ($matches[0] as $key => $match) {
            if (isset($array[$matches[1][$key]])) {
                $content = $array[$matches[1][$key]];

                $url = $this->getRouter()->generate('_content', ['slug' => $content->getSlug()]);
            } else {
                $url = $this->getRouter()->generate('_content', ['slug' => '404']);
            }

            $string = str_replace($match, $url, $string);
        }

        return $string;
    }

    /**
     * @return RouterInterface
     */
    protected function getRouter()
    {
        return $this->container->get('router');
    }
}