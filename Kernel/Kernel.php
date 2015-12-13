<?php

namespace Opifer\CmsBundle\Kernel;

use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

abstract class Kernel extends BaseKernel
{
    /**
     * Register bundles
     *
     * @return array
     */
    public function registerBundles()
    {
        $bundles = array(
            // Symfony standard bundles
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Added vendor bundles
            new \Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle(),
            new \Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
            new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new \FOS\UserBundle\FOSUserBundle(),
            new \Genemu\Bundle\FormBundle\GenemuFormBundle(),
            new \Infinite\FormBundle\InfiniteFormBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle($this),
            new \Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new \Liip\ImagineBundle\LiipImagineBundle(),
            new \Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new \Presta\SitemapBundle\PrestaSitemapBundle(),
            new \Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),

            // Opifer bundles
            new \Opifer\CmsBundle\OpiferCmsBundle(),
            new \Opifer\ContentBundle\OpiferContentBundle(),
            new \Opifer\EavBundle\OpiferEavBundle(),
            new \Opifer\FormBundle\OpiferFormBundle(),
            new \Opifer\MediaBundle\OpiferMediaBundle(),
            new \Opifer\RedirectBundle\OpiferRedirectBundle(),
            new \Opifer\RulesEngineBundle\OpiferRulesEngineBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * Register container config
     *
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     *
     * @return void
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
