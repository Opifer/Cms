<?php

namespace Opifer\EavBundle\Manager;

use Opifer\EavBundle\Model\EntityInterface;
use Opifer\EavBundle\Model\TemplateInterface;
use Opifer\EavBundle\Model\ValueSetInterface;
use Opifer\EavBundle\ValueProvider\Pool;
use Opifer\EavBundle\Form\Type\NestedType;

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

    /**
     * Get the formdata related to the level and optionally the parent
     *
     * @param array $formdata
     * @param string $formdata
     * @param int $level
     * @param string $parent
     *
     * @return array
     */
    public function getFormDataByLevel($formdata, $attribute, $level = 1, $parent)
    {
        $collection = [];
        foreach ($formdata as $key => $data) {
            if (!strpos($key, NestedType::SEPARATOR) || strpos($key, 'valueset_namedvalues')) {
                continue;
            }

            $keys = $this->parseNestedTypeName($key);

            if ($keys['level'] == $level && $keys['attribute'] == $attribute) {
                $pos = strpos($key, $parent);
                if (($level > 1 && ($pos !== false)) || $level == 1) {
                    $collection[$key] = $data;
                }
            }
        }

        return $collection;
    }

    /**
     * Generate a unique name for the nested item
     *
     * In case of newly added nested content, we need to add an index
     * to the form type name, to avoid same template name conflicts.
     *
     * @param string $attribute
     * @param int|string $id
     * @param int $index
     * @param null|string $parent
     *
     * @return string
     */
    public function generateNestedTypeName($attribute, $id, $index, $parent = null)
    {
        if ($parent) {
            $parsedParent = explode('_valueset', $parent);
            $parent = array_shift($parsedParent);
        } else {
            $parent = 'nested_content';
        }

        $key = implode(NestedType::SEPARATOR, [$parent, $attribute, $id, $index]);

        return $key;
    }

    /**
     * Parses the key string to a usable array
     *
     * @param string $name
     *
     * @return array
     */
    public function parseNestedTypeName($name)
    {
        $keys = explode(NestedType::SEPARATOR, $name);
        array_shift($keys);

        return [
            'level' => (count($keys) / 3),
            'index' => (int) end($keys),
            'reference' => prev($keys),
            'attribute' => prev($keys),
            'key' => $name
        ];
    }
}
