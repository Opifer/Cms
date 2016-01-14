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
     * Create a new redirect object
     *
     * @return Redirect
     */
    public function createNew();

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

    /**
     * Transform a Redirect into valid Symfony route requirements
     *
     * @param Redirect $redirect
     * @return array
     */
    public function formatRouteRequirements(Redirect $redirect);
}
