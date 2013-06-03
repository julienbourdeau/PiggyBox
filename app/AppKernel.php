<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new PiggyBox\OrderBundle\PiggyBoxOrderBundle(),
            new PiggyBox\ShopBundle\PiggyBoxShopBundle(),
            new PiggyBox\UserBundle\PiggyBoxUserBundle(),
            new PiggyBox\SitemapBundle\PiggyBoxSitemapBundle(),

            //third-party bundle
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Sonata\SeoBundle\SonataSeoBundle(),
            new BCC\CronManagerBundle\BCCCronManagerBundle(),
            new Craue\TwigExtensionsBundle\CraueTwigExtensionsBundle(),
            new WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),
            new Ivory\GoogleMapBundle\IvoryGoogleMapBundle(),
            new Liip\CodeBundle\LiipCodeBundle(),
            new FOS\FacebookBundle\FOSFacebookBundle(),

            //bundles for category in PiggyBoxShopBundle
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\Bundle\TimeBundle\KnpTimeBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
