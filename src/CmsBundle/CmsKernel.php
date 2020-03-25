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
            \Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
            \Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
            \Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
            \Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
            \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class => ['all' => true],
            \Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
            \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],

            // Added vendor bundles
            \APY\DataGridBundle\APYDataGridBundle::class => ['all' => true],
            \Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle::class => ['all' => true],
//            new \Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle::class => ['all' => true],
            \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
            \FOS\JsRoutingBundle\FOSJsRoutingBundle::class => ['all' => true],
            \FOS\RestBundle\FOSRestBundle::class => ['all' => true],
            \FOS\UserBundle\FOSUserBundle::class => ['all' => true],
            \JMS\SerializerBundle\JMSSerializerBundle::class => ['all' => true],
            \Knp\Bundle\GaufretteBundle\KnpGaufretteBundle::class => ['all' => true],
            \Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle::class => ['all' => true],
            \Lexik\Bundle\TranslationBundle\LexikTranslationBundle::class => ['all' => true],
            \Liip\ImagineBundle\LiipImagineBundle::class => ['all' => true],
            \Limenius\LiformBundle\LimeniusLiformBundle::class => ['all' => true],
            \Nelmio\ApiDocBundle\NelmioApiDocBundle::class => ['all' => true],
            \Nelmio\CorsBundle\NelmioCorsBundle::class => ['all' => true],
            \Scheb\TwoFactorBundle\SchebTwoFactorBundle::class => ['all' => true],
            \Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle::class => ['all' => true],

            // Opifer bundles
            \Opifer\CmsBundle\OpiferCmsBundle::class => ['all' => true],
            \Opifer\ContentBundle\OpiferContentBundle::class => ['all' => true],
            \Opifer\EavBundle\OpiferEavBundle::class => ['all' => true],
            \Opifer\FormBundle\OpiferFormBundle::class => ['all' => true],
            \Opifer\FormBlockBundle\OpiferFormBlockBundle::class => ['all' => true],
            \Opifer\MediaBundle\OpiferMediaBundle::class => ['all' => true],
            \Opifer\RedirectBundle\OpiferRedirectBundle::class => ['all' => true],
            \Opifer\ReviewBundle\OpiferReviewBundle::class => ['all' => true],
            \Opifer\MailingListBundle\OpiferMailingListBundle::class => ['all' => true],
            \Opifer\Revisions\OpiferRevisionsBundle::class => ['all' => true],
            \Opifer\BootstrapBlockBundle\OpiferBootstrapBlockBundle::class => ['all' => true],

            \Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'test' => true],
            \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
        ];

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
