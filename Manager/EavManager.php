<?php

namespace Opifer\EavBundle\Manager;

use Opifer\EavBundle\Eav\EntityInterface;
use Opifer\EavBundle\Entity\Template;
use Opifer\EavBundle\Entity\ValueSet;
use Opifer\EavBundle\ValueProvider\Pool;

class EavManager
{
    /** @var \Opifer\EavBundle\ValueProvider\Pool */
    protected $providerPool;

    /**
     * Constructor
     *
     * @param Pool $providerPool
     */
    public function __construct(Pool $providerPool)
    {
        $this->providerPool = $providerPool;
    }

    /**
     * Initializes an entity from a template to work properly with this bundle.
     *
     * @param Template $template
     *
     * @return EntityInterface
     */
    public function initializeEntity(Template $template)
    {
        $valueSet = new ValueSet();
        $valueSet->setTemplate($template);

        // To avoid persisting Value entities with no actual value to the database
        // we create empty ones, that will be removed on postPersist events.
        $this->replaceEmptyValues($valueSet);

        $entity = $template->getObjectClass();
        $entity = new $entity();

        if (!$entity instanceof EntityInterface) {
            throw new \Exception('The entity specified in the "'.$template->getName().'" template must implement Opifer\EavBundle\Eav\EntityInterface.');
        }

        $entity->setTemplate($template);
        $entity->setValueSet($valueSet);

        return $entity;
    }

    /**
     * Creates empty entities for non-persisted attributes.
     *
     * @param ValueSet $valueSet
     *
     * @return array
     */
    public function replaceEmptyValues(ValueSet $valueSet)
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
