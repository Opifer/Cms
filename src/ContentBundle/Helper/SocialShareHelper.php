<?php

namespace Opifer\ContentBundle\Helper;

class SocialShareHelper
{
    /**
     * @param string $url
     *
     * @return string
     */
    public function getFacebookShareUrl($url)
    {
        return sprintf('https://www.facebook.com/sharer.php?u=%s', urlencode($url));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getTwitterShareUrl($url)
    {
        return sprintf('https://twitter.com/intent/tweet?text=%s', urlencode($url));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getGoogleShareUrl($url)
    {
        return sprintf('https://plus.google.com/share?url=%s', urlencode($url));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getLinkedInShareUrl($url)
    {
        return sprintf('https://www.linkedin.com/shareArticle?mini=true&amp;url=%s', urlencode($url));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getWhatsappShareUrl($url)
    {
        return sprintf('whatsapp://send?text=%s', urlencode($url));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getEmailShareUrl($url)
    {
        return sprintf('mailto:?subject=&amp;body=%s', urlencode($url));
    }
}
