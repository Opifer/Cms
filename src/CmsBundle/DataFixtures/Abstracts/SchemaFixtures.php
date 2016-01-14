<?php

namespace Opifer\CmsBundle\DataFixtures\Abstracts;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Opifer\CmsBundle\Entity\Layout;
use Opifer\CmsBundle\Entity\Schema;
use Opifer\CmsBundle\Entity\Attribute;
use Opifer\CmsBundle\Entity\Option;

abstract class SchemaFixtures extends AbstractFixture implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Main method used by children
     */
    abstract protected function update();

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Helper for retrieving an entity or using new one
     *
     * @param object $entityObject
     * @param string $name
     *
     * @return object
     */
    protected function getEntityByName($entityObject, $name)
    {
        $entity = $this->entityManager->getRepository(get_class($entityObject))->findBy(['name' => $name]);

        if (isset($entity[0])) {
            return $entity[0];
        }

        return $entityObject;
    }

    /**
     * Helper for retrieving an Option entity or using new one
     *
     * @param object $optionObject
     * @param object $attributeEntity
     * @param string $name
     *
     * @return string
     */
    protected function getOptionByName($optionObject, $attributeEntity, $name)
    {
        $option = $this->entityManager->getRepository(get_class($optionObject))->findBy([
            'name' => $name,
            'attribute' => $attributeEntity,
        ]);

        if (isset($option[0])) {
            return $option[0];
        }

        return $optionObject;
    }

    /**
     * Helper for retrieving an schema entity
     *
     * @param  string           $name
     * @return Schema|boolean
     */
    protected function getSchema($name)
    {
        $schemaEntity = $this->getEntityByName(new Schema(), $name);
        if ($schemaEntity->getName() == $name) {
            return $schemaEntity;
        }

        return false;
    }

    /**
     * Helper for retrieving an layout entity
     *
     * @param string $name
     *
     * @return Layout|boolean
     */
    protected function getLayout($name)
    {
        $layoutEntity = $this->getEntityByName(new Layout(), $name);
        if ($layoutEntity->getName() == $name) {
            return $layoutEntity;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');

        $this->update();

        $this->entityManager->flush();
    }

    /**
     * Update schema attribute if it exists, works on already added attributes
     *
     * @param object $schemaEntity
     * @param object $attribute
     * @param object $values
     */
    protected function updateSchemaAttribute($schemaEntity, $attribute, $values = [])
    {
        if ($attributeEntity = $schemaEntity->getAttribute($attribute)) {
            if ($attributeEntity->getId()) {
                foreach ($values as $key => $value) {
                    $update = 'set'.ucfirst($key);
                    $attributeEntity->$update($value);
                }

                $this->entityManager->persist($attributeEntity);
            } else {
                $schemaEntity->removeAttribute($attributeEntity);
            }
        }
    }

    /**
     * Helper for adding layouts
     *
     * @param string $name
     * @param array  $values
     *
     * @return Layout
     */
    protected function addLayout($name, $values)
    {
        $layoutEntity = $this->getEntityByName(new Layout(), $name);

        $layoutEntity->setName($name);

        foreach ($values as $key => $value) {
            $update = 'set'.ucfirst($key);
            $layoutEntity->$update($value);
        }

        $this->entityManager->persist($layoutEntity);

        return $layoutEntity;
    }

    /**
     * Helper for adding schemas
     *
     * @param string $name
     * @param array  $values
     * @param Layout $layoutEntity
     *
     * @return Schema
     */
    protected function addSchema($name, $values, $layoutEntity = false)
    {
        $serializer = $this->container->get('jms_serializer');

        $schemaEntity = $this->getEntityByName(new Schema(), $name);

        $schemaEntity->setName($name);

        foreach ($values as $key => $value) {
            $update = 'set'.ucfirst($key);
            $schemaEntity->$update($value);
        }

        if ($layoutEntity !== false) {
            if (!$layoutEntity->getId()) {
                $this->entityManager->flush();
            }

            $schemaEntity->setPresentation($serializer->serialize($layoutEntity, 'json'));
        }

        $this->entityManager->persist($schemaEntity);

        return $schemaEntity;
    }

    /**
     * Helper for setting schema attributes
     *
     * @param Schema $schemaEntity
     * @param array    $attributes
     *
     * @return array
     */
    protected function addSchemaAttributes($schemaEntity, $attributes)
    {
        foreach ($attributes as $id => $attribute) {
            if (!$attributeEntity = $schemaEntity->getAttribute($attribute['name'])) {
                $attributeEntity = new Attribute();
                $schemaEntity->addAttribute($attributeEntity);
            }

            $attributeEntity
                ->setValueType($attribute['type'])
                ->setName($attribute['name'])
                ->setDisplayName($attribute['displayName'])
                ->setSort($attribute['sort'])
                ->setSchema($schemaEntity);

            if (isset($attribute['description'])) {
                $attributeEntity->setDescription($attribute['description']);
            }

            if (isset($attribute['allowedSchemas']) && is_array($attribute['allowedSchemas'])) {
                foreach ($attribute['allowedSchemas'] as $allowedReference) {
                    $attributeEntity->addAllowedSchema($this->getReference($allowedReference));
                }
            }

            if (isset($attribute['options']) && is_array($attribute['options'])) {
                $i = 1;
                foreach ($attribute['options'] as $key => $option) {
                    if (!$optionEntity = $attributeEntity->getOptionByName($key)) {
                        $optionEntity = new Option();
                        $attributeEntity->addOption($optionEntity);
                    }

                    $optionEntity
                        ->setName($key)
                        ->setDisplayName($option)
                        ->setSort($i*10)
                        ->setAttribute($attributeEntity);

                    $attributeEntity->addOption($optionEntity);
                    $this->entityManager->persist($optionEntity);

                    $i++;
                }
            }

            $this->entityManager->persist($attributeEntity);
        }

        $this->entityManager->persist($schemaEntity);

        return $schemaEntity->getAttributes();
    }
}
