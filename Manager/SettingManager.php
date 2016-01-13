<?php

namespace Opifer\CmsBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Opifer\CmsBundle\Entity\Setting;

class SettingManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * This repository is defined as a service, so it can be used like:
     * $container->get('opifer.cms.settings')->get('settingname').
     *
     * @param string $name The unique setting name
     *
     * @return string
     */
    public function get($name)
    {
        if (false === strpos($name, '.')) {
            throw new \InvalidArgumentException(sprintf('Parameter must be in format "namespace.name", "%s" given', $name));
        }

        $setting = $this->getRepository()->findOneBy(array('name' => $name));

        if ($setting === null) {
            throw $this->createNotFoundException($name);
        }

        return $setting->getValue();
    }

    /**
     * @param string      $name  Name of the setting to update.
     * @param string|null $value New value for the setting.
     *
     * @throws \RuntimeException If the setting is not defined.
     */
    public function set($name, $value)
    {
        $setting = $this->getRepository()->findOneBy(array('name' => $name));

        if ($setting === null) {
            throw $this->createNotFoundException($name);
        }

        $setting->setValue($value);
        $this->em->flush($setting);
    }

    /**
     * @param array $newSettings List of settings (as name => value) to update.
     *
     * @throws \RuntimeException If a setting is not defined.
     */
    public function setMultiple(array $newSettings)
    {
        if (empty($newSettings)) {
            return;
        }

        $settings = $this->em->createQueryBuilder()
            ->select('s')
            ->from(get_class(new Setting()), 's', 's.name')
            ->where('s.name IN (:names)')
            ->getQuery()
            ->execute(array('names' => array_keys($newSettings)))
        ;

        foreach ($newSettings as $name => $value) {
            if (!isset($settings[$name])) {
                throw $this->createNotFoundException($name);
            }

            $settings[$name]->setValue($value);
        }

        $this->em->flush();
    }

    /**
     * @return array with name => value
     */
    public function all()
    {
        $settings = array();
        foreach ($this->getRepository()->findAll() as $setting) {
            $settings[$setting->getName()] = $setting->getValue();
        }

        return $settings;
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = $this->em->getRepository(get_class(new Setting()));
        }

        return $this->repository;
    }

    /**
     * @param string $name Name of the setting.
     *
     * @return \RuntimeException
     */
    protected function createNotFoundException($name)
    {
        return new \RuntimeException(sprintf('Setting "%s" couldn\'t be found.', $name));
    }
}
