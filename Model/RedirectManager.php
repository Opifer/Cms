<?php

namespace Opifer\RedirectBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;

class RedirectManager implements RedirectManagerInterface
{
    /** @var ObjectManager */
    protected $om;

    /** @var string */
    protected $class;

    /**
     * Constructor
     *
     * @param ObjectManager $om
     * @param string $class
     *
     * @throws \Exception
     */
    public function __construct(ObjectManager $om, $class)
    {
        if (!is_subclass_of($class, 'Opifer\RedirectBundle\Model\Redirect')) {
            throw new \Exception($class .' must implement Opifer\RedirectBundle\Model\Redirect');
        }

        $this->class = $class;
        $this->om = $om;
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function getRepository()
    {
        return $this->om->getRepository($this->getClass());
    }

    /**
     * {@inheritDoc}
     */
    public function createNew()
    {
        $class    = $this->getClass();
        $redirect = new $class();

        return $redirect;
    }

    /**
     * {@inheritDoc}
     */
    public function save(Redirect $redirect)
    {
        if (!$redirect->getId()) {
            $this->om->persist($redirect);
        }
        $this->om->flush();

        return $redirect;
    }

    /**
     * {@inheritDoc}
     */
    public function remove(Redirect $redirect)
    {
        $this->om->remove($redirect);
        $this->om->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function formatRouteRequirements(Redirect $redirect)
    {
        $requirements = $redirect->getRequirements();
        $routeRequirements = [];

        foreach ($requirements as $r) {
            $routeRequirements[$r['parameter']] = $r['value'];
        }

        return $routeRequirements;
    }
}
