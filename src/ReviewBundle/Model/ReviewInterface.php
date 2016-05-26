<?php

namespace Opifer\ReviewBundle\Model;

interface ReviewInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getContent();

    /**
     * @return string
     */
    public function getAuthor();
}
