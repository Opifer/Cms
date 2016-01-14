<?php

namespace Opifer\MediaBundle\Provider;

/**
 * This pool holds all the service providers tagged with 'opifer.media.provider'
 */
class Pool
{
    /**
     * @var  array
     */
    protected $providers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->providers = array();
    }

    /**
     * Adds all the providers, tagged with 'opifer.media.provider' to the
     * provider pool
     *
     * @param ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider, $alias)
    {
        $this->providers[$alias] = $provider;
    }

    /**
     * Get provider by its alias
     *
     * @param string $alias
     *
     * @return Opifer\MediaBundle\Provider\ProviderInterface
     */
    public function getProvider($alias)
    {
        return $this->providers[$alias];
    }

    /**
     * Get all registered providers
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }
}
