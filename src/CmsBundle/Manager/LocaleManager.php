<?php

namespace Opifer\CmsBundle\Manager;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\TranslationBundle\Manager\LocaleManager as BaseLocaleManager;

/**
 * Manager for translations files.
 */
class LocaleManager extends BaseLocaleManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $locales;

    /**
     * Constructor
     *
     * @param array $managedLocales
     */
    public function __construct(array $managedLocales, EntityManager $em)
    {
        parent::__construct($managedLocales);

        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        if ($this->locales == null) {
            $dynamicLocales = $this->em->getRepository('Opifer\CmsBundle\Entity\Locale')->findAll();

            $this->locales = [];
            foreach ($dynamicLocales as $locale) {
                $this->locales[] = $locale->getLocale();
            }
        }

        return $this->locales;
    }
}
