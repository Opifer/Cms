<?php

namespace Opifer\RedirectBundle\Model;

interface RedirectManagerInterface
{
    /**
     * Get the class
     *
     * @return string
     */
    public function getClass();

    /**
     * Get repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository();

    /**
     * Save a redirect
     *
     * @param Redirect $redirect
     */
    public function save(Redirect $redirect);

    /**
     * Remove a redirect
     *
     * @param Redirect $redirect
     */
    public function remove(Redirect $redirect);
}
