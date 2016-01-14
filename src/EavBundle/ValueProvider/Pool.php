<?php

namespace Opifer\EavBundle\ValueProvider;

/**
 * This pool holds all the service values tagged with 'opifer.eav.value_provider'
 */
class Pool
{
    /** @var array */
    protected $values;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->values = array();
    }

    /**
     * Adds all the values, tagged with 'opifer.media.value' to the
     * value pool
     *
     * @param ValueProviderInterface $value
     */
    public function addValue(ValueProviderInterface $value, $alias)
    {
        if (false === $value->isEnabled()) {
            return;
        }

        $this->values[$alias] = $value;
    }

    /**
     * Get value by its alias
     *
     * @param string $alias
     *
     * @return Opifer\EavBundle\ValueProvider\ValueProviderInterface
     */
    public function getValue($alias)
    {
        return $this->values[$alias];
    }

    /**
     * Get a valueprovider by it's entity class
     *
     * @param string|object $entity
     *
     * @return ValueProviderInterface
     */
    public function getValueByEntity($entity)
    {
        if (is_object($entity)) {
            $entity = get_class($entity);
        }

        foreach ($this->getValues() as $provider) {
            if ($entity === $provider->getEntity()) {
                return $provider;
            }
        }

        return false;
    }

    /**
     * Get all registered values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}
