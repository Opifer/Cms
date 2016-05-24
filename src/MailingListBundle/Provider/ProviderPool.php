<?php

namespace Opifer\MailingListBundle\Provider;

/**
 * This pool holds the services tagged with 'opifer.mailinglist.provider'
 */
class ProviderPool
{
    /** @var MailingListProviderInterface[] */
    protected $providers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->providers = array();
    }

    /**
     * Adds all the providers, tagged with 'opifer.mailinglist.provider' to the
     * provider pool
     *
     * @param MailingListProviderInterface  $value
     * @param string                        $alias
     */
    public function addProvider(MailingListProviderInterface $value, $alias)
    {
        $this->providers[$alias] = $value;
    }

    /**
     * Get provider by its alias
     *
     * @param string $alias
     *
     * @return MailingListProviderInterface
     */
    public function getProvider($alias)
    {
        return $this->providers[$alias];
    }

    /**
     * Get all registered providers
     *
     * @return MailingListProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }
}
