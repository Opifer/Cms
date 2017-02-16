<?php

namespace Opifer\CmsBundle\Manager;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\TranslationBundle\Manager\LocaleManager as BaseLocaleManager;
use Opifer\CmsBundle\Entity\Locale;

/**
 * Locale Manager.
 */
class LocaleManager extends BaseLocaleManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Locale[]
     */
    protected $locales;

    /**
     * The entity manager is being passed to this manager by a compiler pass.
     *
     * @see Opifer\CmsBundle\DependencyInjection\Compiler\VendorCompilerPass
     *
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        if ($this->locales == null) {
            /** @var Locale[] $dynamicLocales */
            $dynamicLocales = $this->em->getRepository(Locale::class)->findAll();

            $this->locales = [];
            foreach ($dynamicLocales as $locale) {
                $this->locales[] = $locale->getLocale();
            }
        }

        return $this->locales;
    }
}
