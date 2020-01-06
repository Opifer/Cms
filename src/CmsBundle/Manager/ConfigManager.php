<?php

namespace Opifer\CmsBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Opifer\CmsBundle\Entity\Config;

class ConfigManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var Config[]
     */
    protected $configs;

    /**
     * @param EntityManager $em
     * @param string        $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (null === $this->configs) {
            $this->loadConfigs();
        }

        if (false == array_key_exists($key, $this->configs)) {
            $availableKeys = implode(', ', array_keys($this->configs));
            throw new \RuntimeException(sprintf('Config "%s" could not be found. Found: %s', $key, $availableKeys));
        }

        $config = $this->configs[$key];

        if (!$config->getValue() instanceof \stdClass || !isset($config->getValue()->Value)) {
            new \Exception(sprintf('Config "%s" should be stored as \stdClass with a Value property', $key));
        }

        return ($config->getValue() instanceof \stdClass) ? $config->getValue()->Value : $config->getValue();
    }

    /**
     * @param string $key
     *
     * @return Config
     */
    public function getConfig($key)
    {
        if (null === $this->configs) {
            $this->loadConfigs();
        }

        if (false == array_key_exists($key, $this->configs)) {
            $availableKeys = implode(', ', array_keys($this->configs));
            throw new \RuntimeException(sprintf('Config "%s" could not be found. Found: %s', $key, $availableKeys));
        }

        $config = $this->configs[$key];

        return $config;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $config = $this->getConfig($key);

        $object = new \stdClass();
        $object->Value = $value;

        $config->setValue($object);

        $this->em->merge($config);
        $this->em->flush();
    }

    /**
     * @param string $key
     *
     * @return Config
     */
    public function findOrCreate($key)
    {
        if ($this->exists($key)) {
            return $this->configs[$key];
        }

        $config = new $this->class();
        $config->setName($key);

        return $config;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        if (null === $this->configs) {
            $this->loadConfigs();
        }

        return array_key_exists($key, $this->configs);
    }

    /**
     * Returns a key-value array of the settings.
     *
     * @param string $form
     *
     * @return array
     */
    public function keyValues($form = null)
    {
        if (null === $this->configs) {
            $this->loadConfigs();
        }

        $keys = ($form) ? call_user_func([$form, 'getFields']) : [];

        $array = [];
        foreach ($this->configs as $key => $config) {
            if ($form && !in_array($key, $keys)) {
                continue;
            }

            $array[$key] = $config->getValue();
        }

        return $array;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->exists($key);
    }

    /**
     * Retrieve the configs and converts them for later use.
     */
    public function loadConfigs()
    {
        $configs = [];
        foreach ($this->getRepository()->findAll() as $config) {
            $configs[$config->getName()] = $config;
        }

        $this->configs = $configs;

        return $configs;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getClass());
    }
}
