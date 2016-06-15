<?php

namespace Opifer\ExpressionEngine\Prototype;

class PrototypeCollection
{
    /** @var Prototype[] */
    protected $collection = [];

    /** @var int */
    protected $i = 0;

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
     */
    public function add(Prototype $prototype)
    {
        $prototype->setKey($this->i);

        $this->collection[] = $prototype;

        ++$this->i;
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
}
