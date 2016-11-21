<?php

namespace Opifer\CmsBundle;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

abstract class CmsKernel extends BaseKernel
{
    /**
     * Register bundles.
     *
     * @return array
     */
    public function registerBundles()
    {
        $bundles = [
            // Symfony standard bundles
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Added vendor bundles
            new \APY\DataGridBundle\APYDataGridBundle(),
            new \Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle(),
            new \Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new \FOS\UserBundle\FOSUserBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \Liip\ImagineBundle\LiipImagineBundle(),
            new \Nelmio\CorsBundle\NelmioCorsBundle(),
            new \Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),

            // Opifer bundles
            new \Opifer\CmsBundle\OpiferCmsBundle(),
            new \Opifer\ContentBundle\OpiferContentBundle(),
            new \Opifer\EavBundle\OpiferEavBundle(),
            new \Opifer\FormBundle\OpiferFormBundle(),
            new \Opifer\FormBlockBundle\OpiferFormBlockBundle(),
            new \Opifer\MediaBundle\OpiferMediaBundle(),
            new \Opifer\RedirectBundle\OpiferRedirectBundle(),
            new \Opifer\ReviewBundle\OpiferReviewBundle(),
            new \Opifer\MailingListBundle\OpiferMailingListBundle(),
            new \Opifer\Revisions\OpiferRevisionsBundle(),
            new \Opifer\BootstrapBlockBundle\OpiferBootstrapBlockBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * Register container config.
     *
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
