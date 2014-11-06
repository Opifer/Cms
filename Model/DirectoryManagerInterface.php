<?php

namespace Opifer\ContentBundle\Model;

interface DirectoryManagerInterface
{
    /**
     * Get class
     *
     * @return string
     */
    public function getClass();

    /**
     * Create a new directory instance
     *
     * @return DirectoryInterface
     */
    public function create();

    /**
     * Save directory
     *
     * @param  DirectoryInterface $directory
     *
     * @return DirectoryInterface
     */
    public function save(DirectoryInterface $directory);

    /**
     * Get repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository();
}
