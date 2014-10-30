<?php

namespace Opifer\EavBundle\Manager;

use Opifer\EavBundle\Model\EntityInterface;
use Opifer\EavBundle\Model\TemplateInterface;
use Opifer\EavBundle\Model\ValueSetInterface;
use Opifer\EavBundle\ValueProvider\Pool;

class EavManager
{
    /** @var \Opifer\EavBundle\ValueProvider\Pool */
    protected $providerPool;

    /** @var string */
    protected $valueSetClass;

    /**
     * Constructor
     *
     * @param Pool $providerPool
     */
    public function __construct(Pool $providerPool, $valueSetClass)
    {
        $this->providerPool = $providerPool;
        $this->valueSetClass = $valueSetClass;
    }

    /**
     * Initializes an entity from a template to work properly with this bundle.
     *
     * @param Template $template
     *
     * @return EntityInterface
     */
    public function initializeEntity(TemplateInterface $template)
    {
        $valueSetClass = $this->valueSetClass;
        $valueSet = new $valueSetClass();
        $valueSet->setTemplate($template);

        // To avoid persisting Value entities with no actual value to the database
        // we create empty ones, that will be removed on postPersist events.
        $this->replaceEmptyValues($valueSet);

        $entity = $template->getObjectClass();
        $entity = new $entity();

        if (!$entity instanceof EntityInterface) {
            throw new \Exception('The entity specified in the "'.$template->getName().'" template must implement Opifer\EavBundle\Eav\EntityInterface.');
        }

        $entity->setValueSet($valueSet);
        $entity->setTemplate($template);

        return $entity;
    }

    /**
     * Creates empty entities for non-persisted attributes.
     *
     * @param ValueSetInterface $valueSet
     *
     * @return array
     */
    public function replaceEmptyValues(ValueSetInterface $valueSet)
    {
        // collect persisted attributevalues
        $persistedAttributes = array();
        foreach ($valueSet->getValues() as $value) {
            $persistedAttributes[] = $value->getAttribute();
        }

        $newValues = array();

        // Create empty entities for missing attributes
        $missingAttributes = array_diff($valueSet->getAttributes()->toArray(), $persistedAttributes);
        foreach ($missingAttributes as $attribute) {

            $provider = $this->providerPool->getValue($attribute->getValueType());
            $valueClass = $provider->getEntity();

            $value = new $valueClass();
            $valueSet->addValue($value);
            $value->setValueSet($valueSet);
            $value->setAttribute($attribute);

            $newValues[] = $value;
        }

        return $newValues;
    }
}
