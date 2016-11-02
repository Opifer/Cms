<?php

namespace Opifer\ExpressionEngine\Prototype;

class PrototypeCollection
{
    /** @var Prototype[] */
    protected $collection = [];

    /**
     * Constructor.
     *
     * @param Prototype[]|null $collection
     */
    public function __construct(array $collection = null)
    {
        if ($collection) {
            foreach ($collection as $prototype) {
                $this->add($prototype);
            }
        }
    }

    /**
     * Adds a prototype to the collection and increments the count.
     *
     * @param Prototype $prototype
     *
     * @throws \Exception If the a prototype with the current key already exists
     */
    public function add(Prototype $prototype)
    {
        if ($this->has($prototype->getKey())) {
            throw new \Exception(sprintf('A prototype with the key %s already exists'));
        }

        $this->collection[] = $prototype;
    }

    /**
     * Returns the complete prototype collection.
     *
     * @return Prototype[]
     */
    public function all()
    {
        return $this->collection;
    }

    /**
     * Check if the collection has a prototype with the given key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        foreach ($this->collection as $prototype) {
            if ($key == $prototype->getKey()) {
                return true;
            }
        }

        return false;
    }
}
