<?php

namespace Opifer\CmsBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;

class DynamicConfiguration
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var
     */
    private $settings;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (null === $this->settings) {
            $this->loadSettings();
        }

        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        }
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        if (null === $this->settings) {
            $this->loadSettings();
        }

        return array_key_exists($key, $this->settings);
    }

    /**
     *  Retrieve the settings and converts them for later use.
     */
    public function loadSettings()
    {
        $settings = array();
        foreach ($this->em->getRepository('OpiferCmsBundle:Setting')->findAll() as $setting) {
            $settings[$setting->getName()] = $setting->getValue();
        }

        $this->settings = $settings;
    }
}
